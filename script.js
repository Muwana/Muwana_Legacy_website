// Function to toggle the menu for small screens
function toggleMenu() {
    const nav = document.querySelector('nav ul');
    nav.classList.toggle('active');
}

// Smooth scroll functionality for anchor links
const links = document.querySelectorAll('a[href^="#"]');
links.forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        window.scrollTo({
            top: targetElement.offsetTop - 50,
            behavior: 'smooth'
        });
    });
});

// Form validation for search
const searchForm = document.querySelector('#search form');
searchForm.addEventListener('submit', function(event) {
    const query = document.querySelector('[name="query"]').value;
    if (query.trim() === "") {
        alert("Please enter a location or property type.");
        event.preventDefault();
    }
});

// Load animation for page load
document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading');
    setTimeout(() => {
        loadingScreen.style.display = 'none';
    }, 2000); // Hide the loading screen after 2 seconds
});

// Add hover effect for gallery images
const galleryImages = document.querySelectorAll('#gallery img');
galleryImages.forEach(image => {
    image.addEventListener('mouseover', () => {
        image.style.transform = 'scale(1.05)';
        image.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.2)';
    });
    image.addEventListener('mouseout', () => {
        image.style.transform = 'scale(1)';
        image.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    });
});
