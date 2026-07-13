// public/js/offlineManager.js

const DB_NAME = "SIMGPI_OfflineDB";
const STORE_NAME = "form_queue";
let db;

// 1. Inisialisasi IndexedDB
const request = indexedDB.open(DB_NAME, 1);
request.onupgradeneeded = (e) => {
    db = e.target.result;
    if (!db.objectStoreNames.contains(STORE_NAME)) {
        db.createObjectStore(STORE_NAME, { keyPath: "id", autoIncrement: true });
    }
};
request.onsuccess = (e) => {
    db = e.target.result;
    updateOfflineBadge();
};

// 2. Cek Antrean Luring
function updateOfflineBadge() {
    const tx = db.transaction([STORE_NAME], "readonly");
    const store = tx.objectStore(STORE_NAME);
    const countReq = store.count();
    
    countReq.onsuccess = () => {
        const count = countReq.result;
        const badge = document.getElementById('universal-offline-badge');
        const countText = document.getElementById('universal-offline-count');
        
        if (badge && countText) {
            if (count > 0) {
                countText.innerText = count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }
    };
}

// 3. Intercept Semua Form dengan class 'offline-ready'
document.addEventListener("DOMContentLoaded", () => {
    const offlineForms = document.querySelectorAll('form.offline-ready');
    
    offlineForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!navigator.onLine) {
                e.preventDefault();
                
                // Kumpulkan data form termasuk URL tujuan (Action) dan Method
                const formData = new FormData(this);
                const payload = {
                    action: this.action,
                    method: this.method || 'POST',
                    timestamp: new Date().getTime(),
                    data: Object.fromEntries(formData.entries())
                };

                const tx = db.transaction([STORE_NAME], "readwrite");
                tx.objectStore(STORE_NAME).add(payload);
                
                tx.oncomplete = () => {
                    alert("ANDA SEDANG OFFLINE!\n\nData berhasil disimpan secara lokal. Sistem akan mensinkronisasikannya secara otomatis saat internet kembali tersedia.");
                    this.reset();
                    updateOfflineBadge();
                };
            }
        });
    });
});

// 4. Sinkronisasi Otomatis Saat Online Kembali
window.addEventListener('online', () => {
    syncAllOfflineData();
});

function syncAllOfflineData() {
    const tx = db.transaction([STORE_NAME], "readonly");
    const store = tx.objectStore(STORE_NAME);
    const getAllReq = store.getAll();

    getAllReq.onsuccess = () => {
        const drafts = getAllReq.result;
        if (drafts.length === 0) return;

        // Tampilkan loading di badge
        const badgeText = document.getElementById('universal-offline-text');
        if(badgeText) badgeText.innerText = "Mensinkronisasikan...";

        let syncCount = 0;
        let hasError = false;

        drafts.forEach(draft => {
            fetch(draft.action, {
                method: draft.method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': draft.data._token
                },
                body: JSON.stringify(draft.data)
            })
            .then(res => {
                if (res.ok) {
                    const delTx = db.transaction([STORE_NAME], "readwrite");
                    delTx.objectStore(STORE_NAME).delete(draft.id);
                } else {
                    hasError = true;
                }
            })
            .catch(() => hasError = true)
            .finally(() => {
                syncCount++;
                if (syncCount === drafts.length) {
                    if (hasError) {
                        alert("Beberapa data gagal disinkronkan ke pangkalan data utama. Pastikan koneksi stabil.");
                    } else {
                        alert("Sinkronisasi Selesai! Semua data luring telah terkirim.");
                        window.location.reload();
                    }
                    updateOfflineBadge();
                }
            });
        });
    };
}