// ===== SmartDine Home Page - Simplified (No LocalStorage / Feedback / Dynamic Loader) =====

// --- Core Initializer ---
document.addEventListener('DOMContentLoaded', () => {
  initializeNavigation();
  initializeScrollFeatures();
  const yearElement = document.getElementById('year');
  if (yearElement) yearElement.textContent = new Date().getFullYear();
});

// --- Navigation Functions ---
function initializeNavigation() {
  // Add any navigation-specific functionality here
  console.log('Navigation initialized');
}

function initializeScrollFeatures() {
  // Add any scroll-related functionality here
  console.log('Scroll features initialized');
}

// CSS keyframes injected for fade-in
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
`;
document.head.appendChild(style);
