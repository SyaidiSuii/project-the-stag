// Add CSS animations
    const accountStyle = document.createElement('style');
    accountStyle.textContent = `
      .account-card {
        animation: fadeInCard 0.6s ease forwards;
      }
      @keyframes fadeInCard {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .profile-header {
        animation: slideInHeader 0.8s ease forwards;
      }
      @keyframes slideInHeader {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .quick-action {
        animation: fadeInAction 0.4s ease forwards;
      }
      @keyframes fadeInAction {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
      }
    `;
    document.head.appendChild(accountStyle);