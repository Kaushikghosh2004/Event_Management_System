document.addEventListener("DOMContentLoaded", function() {
    const forms = document.querySelectorAll("form");
    
    forms.forEach(form => {
        form.onsubmit = function(e) {
            let inputs = form.querySelectorAll("input[required], select[required]");
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.border = "2px solid red";
                    valid = false;
                } else {
                    input.style.border = "1px solid #ccc";
                }
            });

            if (!valid) {
                alert("All mandatory fields must be filled!");
                e.preventDefault();
            }
        };
    });
});