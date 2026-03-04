/**
 * app-offline.js
 * Provides fallback-tolerant API wrapper and localStorage sync
 * Modules can use this to work in both online and offline modes
 */

// Global offline-tolerant API call wrapper
// Usage: apiCall(url, {method:'POST', body:formData}) 
// Returns promise that falls back to localStorage if API fails
async function apiCall(url, opts = {}) {
    opts = Object.assign({method: 'GET', timeout: 5000}, opts);
    // Only use live API/database, no offline fallback
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), opts.timeout);
    try {
        const resp = await fetch(url, {
            ...opts,
            signal: controller.signal
        });
        clearTimeout(id);
        if (!resp.ok) throw new Error(resp.status);
        const result = await resp.json();
        return result;
    } catch (err) {
        clearTimeout(id);
        throw err;
    }
}

// attempt to flush any pending entries when connection returns
async function attemptSync() {
    // iterate through pending keys
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (!key || !key.startsWith('pending_')) continue;
        const formName = key.replace('pending_', '');
        let pending;
        try { pending = JSON.parse(localStorage.getItem(key) || '[]'); } catch(e){ pending=[]; }
        if (!Array.isArray(pending) || pending.length === 0) continue;
        const newPending = [];
        for (const entry of pending) {
            let success = false;
            try {
                const data = entry.data || {};
                const formData = new FormData();
                switch(formName) {
                    case 'sections':
                        formData.append('action','saveSection');
                        formData.append('sec_name', data.section_code || '');
                        formData.append('sec_year', data.year_level || '');
                        // subject/category not used anymore
                        break;
                    case 'rooms':
                        formData.append('action','assignRoom');
                        formData.append('section_id', data.section_id || '');
                        formData.append('room_number', data.room_number || '');
                        break;
                    case 'teachers':
                        formData.append('action','assignTeacher');
                        formData.append('section_id', data.section_id || '');
                        formData.append('faculty_name', data.faculty_name || '');
                        break;
                    default:
                        // unknown form type; keep it pending
                        newPending.push(entry);
                        continue;
                }
                const resp = await fetch('api_' + formName.slice(0, -1) + '.php', {method:'POST', body:formData});
                const json = await resp.json();
                if (json && json.success) {
                    success = true;
                }
            } catch(err) {
                console.warn('Sync failed for', formName, entry, err);
            }
            if (!success) {
                newPending.push(entry);
            }
        }
        if (newPending.length > 0) {
            localStorage.setItem(key, JSON.stringify(newPending));
        } else {
            localStorage.removeItem(key);
        }
    }
}

// automatically attempt to sync periodically when offline
setInterval(function() {
    if (window.APP_OFFLINE && typeof attemptSync === 'function') {
        attemptSync().catch(() => {});
    }
}, 30000); // every 30 seconds


// Fallback data for when API is unavailable
// Reads from localStorage instead
function getFallbackData(url, opts) {
    // determine action from URL or POST data
    let action = 'unknown';
    if (url.includes('?action=')) {
        action = new URLSearchParams(url.split('?')[1]).get('action');
    } else if (opts.body instanceof FormData) {
        action = opts.body.get('action');
    }
    
    // Return cached data from localStorage or empty result
    const cacheKey = 'api_cache_' + action;
    const cached = localStorage.getItem(cacheKey);
    
    if (cached) {
        try {
            return JSON.parse(cached);
        } catch (e) {
            console.warn('Could not parse cached data for', action);
        }
    }
    
    // Default fallback structures
    return {
        success: false,
        offline: true,
        message: 'Database offline. Using local mode.',
        data: []
    };
}

// Save form data to localStorage for later sync
function saveSectionLocally(sectionData, formName = 'sections') {
    const key = 'pending_' + formName;
    let pending = [];
    const existing = localStorage.getItem(key);
    if (existing) {
        try { pending = JSON.parse(existing); } catch (e) {}
    }
    pending.push({
        timestamp: Date.now(),
        data: sectionData
    });
    localStorage.setItem(key, JSON.stringify(pending));
    localStorage.setItem('api_cache_getSections', JSON.stringify({
        success: true,
        data: pending.map((p, i) => ({
            section_id: i + 1,
            ...p.data
        }))
    }));
    return true;
}

// Clear cache
function clearCache() {
    for (let i = localStorage.length - 1; i >= 0; i--) {
        const key = localStorage.key(i);
        if (key && key.startsWith('api_cache_')) {
            localStorage.removeItem(key);
        }
    }
}
