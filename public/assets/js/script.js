

document.addEventListener("DOMContentLoaded", function () {
    const themeCheckbox = document.getElementById("themeCheckbox");

    themeCheckbox.addEventListener("change", function () {
        const isDarkMode = this.checked;
        const theme = isDarkMode ? "dark" : "light";

        // Apply the theme immediately
        document.documentElement.classList.toggle("dark", isDarkMode);

        // Send AJAX request to save the theme
        fetch("/update-theme", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ theme }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                console.log(data.message); // Optional success message
            })
            .catch((error) => {
                console.error("Error updating theme:", error);
            });
    });

        // Change the label text to "Entries per page"
        let labels = document.querySelectorAll("label[for^='dt-length']");
        labels.forEach(label => {
            if (label.innerText.trim() === "entries per page") {
                label.innerText = "Entries per page";
            }
        });

        // Hide alerts after 3 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
        setTimeout(function() {
          alert.style.transition = 'opacity 0.5s';
          alert.style.opacity = '0';
          setTimeout(function() {
            alert.style.display = 'none';
          }, 500);
        }, 3000);
        });



});
