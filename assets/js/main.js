/**
 * Event Management System - Interactive Logic Engine
 * Handles: Password Toggles, Auth Sequences, Form Validation, and UI Feedback.
 */

document.addEventListener("DOMContentLoaded", () => {
    initPasswordToggles();
    initFormValidation();
    initRadioCardInteraction();
    initMembershipCalculator();
});

// --- 1. Password Visibility Toggle ---
function initPasswordToggles() {
    const toggles = document.querySelectorAll(".toggle-password");
    toggles.forEach(icon => {
        icon.addEventListener("click", function() {
            const targetId = this.getAttribute("data-target") || "passInput";
            const input = document.getElementById(targetId);
            
            if (input) {
                const isPass = input.type === "password";
                input.type = isPass ? "text" : "password";
                this.classList.toggle("fa-eye-slash");
                
                // Add a small scale effect on click
                this.style.transform = "translateY(-50%) scale(1.2)";
                setTimeout(() => { this.style.transform = "translateY(-50%) scale(1)"; }, 200);
            }
        });
    });
}

// --- 2. Global Form Validation (Rule #10) ---
function initFormValidation() {
    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll("[required]");

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = "#ef4444"; // Error Red
                    isValid = false;
                } else {
                    field.style.borderColor = "rgba(255,255,255,0.15)"; // Reset to Glass border
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast("All mandatory fields must be filled!", "error");
            } else {
                // Trigger the Authorization Overlay if it exists
                const overlay = document.getElementById("authOverlay");
                if (overlay) {
                    overlay.style.display = "flex";
                }
            }
        });
    });
}

// --- 3. Interactive Radio Cards Handler ---
function initRadioCardInteraction() {
    const radioCards = document.querySelectorAll('input[type="radio"]');
    radioCards.forEach(radio => {
        radio.addEventListener("change", function() {
            // Find the parent maintenance section and add a glow
            const section = this.closest(".maintenance-section");
            if (section) {
                section.style.borderColor = "var(--accent)";
                setTimeout(() => { section.style.borderColor = "var(--glass-border)"; }, 1000);
            }
        });
    });
}

// --- 4. Membership Price Calculator ---
function initMembershipCalculator() {
    const durationRadios = document.querySelectorAll('input[name="duration"]');
    const displayTotal = document.getElementById("displayTotal");

    if (durationRadios.length > 0 && displayTotal) {
        durationRadios.forEach(radio => {
            radio.addEventListener("change", (e) => {
                const price = e.target.getAttribute("data-price");
                if (price) {
                    displayTotal.innerText = `Rs. ${price}/-`;
                    // Interactive Feedback
                    displayTotal.classList.add("pulse-anim");
                    setTimeout(() => { displayTotal.classList.remove("pulse-anim"); }, 500);
                }
            });
        });
    }
}

// --- 5. Toast Notification System (Interactive Feedback) ---
function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className = `glass-toast ${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);

    // Fade in and out
    setTimeout(() => { toast.classList.add("show"); }, 100);
    setTimeout(() => { 
        toast.classList.remove("show");
        setTimeout(() => { toast.remove(); }, 500);
    }, 3000);
}