/* inventory_script.js */

// ==========================================
// 1. MODAL FUNCTIONS
// ==========================================

// --- ADD MODAL ---
var addModal = document.getElementById("addModal");
function openModal() { if(addModal) addModal.style.display = "block"; }
function closeModal() { if(addModal) addModal.style.display = "none"; }

// --- EDIT MODAL ---
var editModal = document.getElementById("editModal");
function openEditModal(id, name, category, quantity, expiry) {
    if(editModal) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_category").value = category;
        document.getElementById("edit_quantity").value = quantity;
        document.getElementById("edit_expiry").value = expiry;
        editModal.style.display = "block";
    }
}
function closeEditModal() { if(editModal) editModal.style.display = "none"; }

// --- DISPENSE MODAL ---
var dispenseModal = document.getElementById("dispenseModal");
function openDispenseModal(id, name, currentQty) {
    if(dispenseModal) {
        document.getElementById("dispense_id").value = id;
        document.getElementById("dispense_name").value = name;
        document.getElementById("dispense_current_qty").value = currentQty;
        var qtyInput = document.getElementsByName("quantity_to_dispense")[0];
        if(qtyInput) qtyInput.max = currentQty;
        dispenseModal.style.display = "block";
    }
}
function closeDispenseModal() { if(dispenseModal) dispenseModal.style.display = "none"; }

// --- REQUEST MODAL ---
var requestModal = document.getElementById("requestModal");
function openRequestModal(id, name) {
    if(requestModal) {
        document.getElementById("request_id").value = id;
        document.getElementById("request_name").value = name;
        requestModal.style.display = "block";
    }
}
function closeRequestModal() { if(requestModal) requestModal.style.display = "none"; }

// ==========================================
// 2. PROFILE DROPDOWN LOGIC
// ==========================================

function toggleDropdown() {
    var dropdown = document.getElementById("userDropdown");
    if (dropdown) {
        // Use 'show' class to match dashboard style
        dropdown.classList.toggle("show");
    }
}

// ==========================================
// 3. GLOBAL CLICK LISTENER
// ==========================================

window.onclick = function(event) {
    // Close Modals
    if (event.target == addModal) closeModal();
    if (event.target == editModal) closeEditModal();
    if (event.target == dispenseModal) closeDispenseModal();
    if (event.target == requestModal) closeRequestModal();

    // Close Dropdown
    if (!event.target.closest('.user-profile')) {
        var dropdowns = document.getElementsByClassName("dropdown-menu");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}