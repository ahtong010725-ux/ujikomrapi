document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(".input input, .input select");

    inputs.forEach(input => {
        if(input.value !== ""){
            input.classList.add("filled");
        }

        input.addEventListener("blur", () => {
            if(input.value !== ""){
                input.classList.add("filled");
            }
        });
    });
});
    