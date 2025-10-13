// ===== Enhanced SmartDine Rewards System =====

const AppData = {
      get: function(key, defaultValue) {
        const data = localStorage.getItem(`smartdine_${key}`);
        try {
            return data ? JSON.parse(data) : defaultValue;
        } catch (e) {
            return defaultValue;
        }
      },
      set: function(key, value) {
        localStorage.setItem(`smartdine_${key}`, JSON.stringify(value));
      }
    };

    // Storage variables - using localStorage
    const rewardsData = window.rewardsData || {};
    let points = rewardsData.isAuthenticated ? rewardsData.points : AppData.get('points', 245);
    let lastCheckinDate = rewardsData.isAuthenticated ? rewardsData.lastCheckinDate : AppData.get('lastCheckinDate', null);
    let checkinStreak = rewardsData.isAuthenticated ? rewardsData.checkinStreak : AppData.get('checkinStreak', 0);
    let vouchers = AppData.get('vouchers', []);

    // Determine if user can check in today (use local timezone, not UTC)
    const today = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD format in local timezone
    let checkedInToday = lastCheckinDate === today;

    // Debug logging
    console.log('Check-in Debug:', {
      lastCheckinDate: lastCheckinDate,
      today: today,
      checkedInToday: checkedInToday,
      checkinStreak: checkinStreak
    });

    // ===== Daily Check-in System =====
    function initializeCheckin() {
      const track = document.getElementById('checkinTrack');
      const checkinPoints = (window.rewardsData && window.rewardsData.checkinSettings && window.rewardsData.checkinSettings.daily_points)
        ? window.rewardsData.checkinSettings.daily_points
        : [25, 5, 5, 10, 10, 15, 20];
      const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      
      track.innerHTML = '';
      days.forEach((day, index) => {
        const dayEl = document.createElement('div');
        dayEl.className = 'day';

        // If checked in today, current streak position should be marked as completed
        // Otherwise, it should be marked as active (ready to check in)
        if (checkedInToday && index <= checkinStreak) {
          // Already checked in today, show completed up to current position
          dayEl.classList.add('completed');
        } else if (!checkedInToday && index === checkinStreak) {
          // Not checked in yet, show current position as active
          dayEl.classList.add('active');
        } else if (!checkedInToday && index < checkinStreak) {
          // Previous days are completed
          dayEl.classList.add('completed');
        } else {
          // Future days are locked
          dayEl.classList.add('locked');
        }

        dayEl.innerHTML = `
          <div class="day-points">+${checkinPoints[index] || 5}</div>
          <div class="day-label">${day}</div>
          <div class="day-number">Day ${index + 1}</div>
        `;
        track.appendChild(dayEl);
      });
      updateStreakStatus();
    }

    // ===== Update Streak Status =====
    function updateStreakStatus() {
      const messageEl = document.getElementById('checkinMessage');
      if (!messageEl) return;
      
      let message = '';
      let showMessage = false;
      
      // Calculate actual streak days (streak is 0-indexed, so add 1 for display)
      const displayStreak = checkedInToday ? checkinStreak + 1 : checkinStreak;

      if (checkinStreak === 0 && !checkedInToday) {
        message = '🌟 Start your check-in streak today!';
        showMessage = true;
      } else if (checkinStreak > 0 && checkinStreak < 7 && !checkedInToday) {
        message = `🔥 Current streak: ${displayStreak} days! Keep it going!`;
        showMessage = true;
      } else if (checkinStreak >= 6 && checkedInToday) {
        message = '🏆 Perfect week! Your streak will reset for new rewards!';
        showMessage = true;
      } else if (checkedInToday) {
        message = `✅ Checked in! Streak: ${displayStreak} day${displayStreak === 1 ? '' : 's'}`;
        showMessage = true;
      }
      
      if (showMessage) {
        messageEl.textContent = message;
        messageEl.style.display = 'block';
      } else {
        messageEl.style.display = 'none';
      }
    }

    // ===== Check-in Functionality =====
    function setupCheckin() {
      const checkInBtn = document.getElementById('checkInBtn');
      if (checkedInToday) {
          checkInBtn.textContent = '✅ Checked In!';
          checkInBtn.disabled = true;
          checkInBtn.classList.remove('pulse');
      }

      checkInBtn.addEventListener('click', () => {
        // Check authentication before allowing check-in
        if (window.requireAuth && !window.requireAuth('checkin', function() {
          performCheckin();
        })) return;
        
        // If no requireAuth function, perform checkin normally (for logged-in users)
        if (!window.requireAuth) {
          performCheckin();
        }
      });

      function createFloatingPoints(points, element) {
        const rect = element.getBoundingClientRect();
        const floatingEl = document.createElement('div');
        floatingEl.className = 'points-float';
        floatingEl.textContent = `+${points} pts!`;
        floatingEl.style.left = `${rect.left + rect.width / 2}px`;
        floatingEl.style.top = `${rect.top}px`;
        floatingEl.style.position = 'fixed';
        floatingEl.style.transform = 'translateX(-50%)';
        document.body.appendChild(floatingEl);

        setTimeout(() => {
          if (document.body.contains(floatingEl)) {
            document.body.removeChild(floatingEl);
          }
        }, 2000);
      }

      function createConfettiEffect(element) {
        const rect = element.getBoundingClientRect();
        const colors = ['#6366f1', '#ff6b35', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

        for (let i = 0; i < 12; i++) {
          const particle = document.createElement('div');
          particle.className = 'confetti-particle';
          particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
          particle.style.left = `${rect.left + rect.width / 2 + (Math.random() - 0.5) * 100}px`;
          particle.style.top = `${rect.top + rect.height / 2}px`;
          particle.style.animationDelay = `${Math.random() * 0.5}s`;
          particle.style.animationDuration = `${1 + Math.random()}s`;

          document.body.appendChild(particle);

          setTimeout(() => {
            if (document.body.contains(particle)) {
              document.body.removeChild(particle);
            }
          }, 2000);
        }
      }

      function performCheckin() {
        if (checkedInToday) {
          showMessage('You have already checked in today! Come back tomorrow! 😊');
          return;
        }

        // Animation: Button press effect
        const btn = document.getElementById('checkInBtn');
        btn.classList.add('checkin-animation');
        btn.style.transform = 'scale(0.95)';
        btn.disabled = true;

        // For authenticated users, call the backend API
        if (rewardsData.isAuthenticated) {
          const csrfToken = rewardsData.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
          fetch(rewardsData.checkinRoute, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            }
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              // Get earned points from response
              const earnedPoints = data.points_earned;

              // Create floating points and confetti animations
              createFloatingPoints(earnedPoints, btn);
              createConfettiEffect(btn);

              setTimeout(() => {
                // Animate the current day completion
                const currentDayElement = document.querySelector('.day.active');
                if (currentDayElement) {
                  currentDayElement.classList.add('completing');
                }

                // Update local variables with server response
                points = data.new_balance;
                checkinStreak = data.streak;
                checkedInToday = data.checked_in_today;
                lastCheckinDate = new Date().toLocaleDateString('en-CA'); // Use local timezone
                AppData.set('lastCheckinDate', lastCheckinDate);
                AppData.set('checkinStreak', checkinStreak);

                // Immediately update points display
                console.log('Updating points to:', points);
                updatePointsDisplay();

                setTimeout(() => {
                  updatePointsDisplay(); // Update again for safety
                  initializeCheckin();
                  renderPointsRewards();
                  updateStreakStatus();

                  // Add glow effect to streak track if it's a significant milestone
                  if (checkinStreak >= 3) {
                    const track = document.getElementById('checkinTrack');
                    track.classList.add('streak-glow');
                    setTimeout(() => track.classList.remove('streak-glow'), 4500);
                  }

                  btn.textContent = '✅ Checked In!';
                  btn.style.transform = '';
                  btn.classList.remove('checkin-animation');
                }, 800);

                showMessage(data.message, 'success');
              }, 300);
            } else {
              // Handle error
              btn.disabled = false;
              btn.style.transform = '';
              btn.classList.remove('checkin-animation');
              showMessage(data.message, 'error');
            }
          })
          .catch(error => {
            console.error('Check-in error:', error);
            btn.disabled = false;
            btn.style.transform = '';
            btn.classList.remove('checkin-animation');
            showMessage('Failed to process check-in. Please try again.', 'error');
          });
        } else {
          let guestCheckinPoints = (window.rewardsData && window.rewardsData.checkinSettings && window.rewardsData.checkinSettings.daily_points)
            ? window.rewardsData.checkinSettings.daily_points
            : [25, 5, 5, 10, 10, 15, 20];
          const earnedPoints = guestCheckinPoints[checkinStreak] || 5;

          // Create floating points and confetti animations
          createFloatingPoints(earnedPoints, btn);
          createConfettiEffect(btn);

          setTimeout(() => {
            // Animate the current day completion
            const currentDayElement = document.querySelector('.day.active');
            if (currentDayElement) {
              currentDayElement.classList.add('completing');
            }

            points += earnedPoints;
            checkinStreak = (checkinStreak + 1) % 7; // Cycle through the week

            AppData.set('points', points);
            AppData.set('lastCheckinDate', new Date().toISOString().split('T')[0]);
            AppData.set('checkinStreak', checkinStreak);

            updatePointsDisplay();

            setTimeout(() => {
              initializeCheckin();
              renderPointsRewards();
              updateStreakStatus();

              // Add glow effect to streak track if it's a significant milestone
              if (checkinStreak >= 3) {
                const track = document.getElementById('checkinTrack');
                track.classList.add('streak-glow');
                setTimeout(() => track.classList.remove('streak-glow'), 4500);
              }

              btn.textContent = '✅ Checked In!';
              btn.style.transform = '';
              btn.classList.remove('checkin-animation');
            }, 800);

            showMessage(`🎉 Check-in successful! +${earnedPoints} points earned!`, 'success');
          }, 300);
        }

        btn.classList.remove('pulse');
      }
    }

    // ===== Points Rewards System =====
    function renderPointsRewards() {
      const list = document.getElementById('redeemList');

      // Skip JavaScript rendering if server has already rendered content
      if (list && list.children.length > 0) {
        return; // Don't override server-rendered content
      }

      const rewards = AppData.get('rewards', []).filter(r => r.status === 'active');

      list.innerHTML = '';
      rewards.forEach(reward => {
        const item = document.createElement('div');
        item.className = 'reward-item';
        
        const canAfford = points >= reward.points;
        
        item.innerHTML = `
          <div class="reward-info">
            <div class="reward-name">${reward.name}</div>
            <div class="reward-cost">${reward.description} • ${reward.points} points</div>
          </div>
          <button class="btn-secondary ${!canAfford ? 'btn-disabled' : ''}" 
                  ${!canAfford ? 'disabled' : ''}>
            ${canAfford ? 'Redeem' : 'Need ' + (reward.points - points)}
          </button>
        `;
        
        const btn = item.querySelector('button');
        if (canAfford) {
          btn.addEventListener('click', () => {
            const redeemAction = () => {
              points -= reward.points;
              AppData.set('points', points);
              // Continue with the rest of the redeem logic
              completeRedemption();
            };
            
            const completeRedemption = () => {
              // Update redeemed count
            let allRewards = AppData.get('rewards', []);
            const rewardIndex = allRewards.findIndex(r => r.id === reward.id);
            if(rewardIndex !== -1) {
                allRewards[rewardIndex].redeemed = (allRewards[rewardIndex].redeemed || 0) + 1;
                AppData.set('rewards', allRewards);
            }

            updatePointsDisplay();
            renderPointsRewards();
            showMessage(`🎉 Redeemed: ${reward.name}! Check your vouchers.`, 'success');
            
            addVoucher({
              name: reward.name,
              description: reward.description,
              expires: getExpiryDate(30),
              type: 'redeemed'
            });
            };
            
            // Check authentication before redeeming
            if (window.requireAuth) {
              window.requireAuth('redeem', redeemAction);
            } else {
              redeemAction();
            }
          });
        }
        
        list.appendChild(item);
      });
    }
    
    // ===== Special Events =====
    function renderSpecialEvents() {
        const list = document.getElementById('event-list');
        const events = AppData.get('events', []).filter(e => e.status === 'active' || e.status === 'coming');
        list.innerHTML = '';
        if (events.length === 0) {
            list.innerHTML = '<p style="text-align: center; color: var(--text-2);">No special events right now. Check back later!</p>';
            return;
        }
        events.forEach(event => {
            const statusClass = event.status === 'active' ? 'status-active' : 'status-coming';
            const item = `
                <div class="event-item">
                  <div class="event-header">
                    <div>
                      <h3 class="event-title">${event.title}</h3>
                      <p class="event-description">${event.description}</p>
                    </div>
                    <div class="event-status ${statusClass}">${event.status.toUpperCase()}</div>
                  </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', item);
        });
    }
    
    // ===== Page Content =====
    function renderPageContent() {
        const content = AppData.get('content', {
            title: '🎁 Rewards & Points',
            pointsLabel: 'Your Points',
            checkinHeader: '📅 Daily Check-In Streak',
            checkinDesc: 'Check in daily to earn bonus points!',
        });
        document.getElementById('categoryTitle').innerHTML = content.title;
        document.getElementById('points-label').textContent = content.pointsLabel;
        document.getElementById('checkin-header').textContent = content.checkinHeader;
        document.getElementById('checkin-desc').textContent = content.checkinDesc;
    }

    // ===== Voucher Collection System =====
    function renderVoucherCollection() {
      const list = document.getElementById('voucherList');
      const offers = [
        {name: '🎟️ RM10 OFF', spend: 50, desc: 'Spend RM50 or more'},
        {name: '🎫 RM20 OFF', spend: 100, desc: 'Spend RM100 or more'},
        {name: '🏷️ RM30 OFF', spend: 150, desc: 'Spend RM150 or more'},
        {name: '💎 RM50 OFF', spend: 250, desc: 'VIP spending reward'}
      ];
      
      list.innerHTML = '';
      offers.forEach(offer => {
        const item = document.createElement('div');
        item.className = 'reward-item';
        item.innerHTML = `
          <div class="reward-info">
            <div class="reward-name">${offer.name}</div>
            <div class="reward-requirement">${offer.desc}</div>
          </div>
          <button class="btn-secondary" onclick="collectVoucher('${offer.name}', '${offer.desc}')">
            Collect
          </button>
        `;
        list.appendChild(item);
      });
    }

    // ===== Voucher Collection =====
    function collectVoucher(name, desc) {
      // Check authentication before collecting voucher
      if (window.requireAuth && !window.requireAuth('collect', function() {
        performVoucherCollection(name, desc);
      })) return;
      
      // If no requireAuth function, perform collection normally (for logged-in users)
      if (!window.requireAuth) {
        performVoucherCollection(name, desc);
      }
    }
    
    function performVoucherCollection(name, desc) {
      const newVoucher = {
        name: name,
        description: desc,
        expires: getExpiryDate(60),
        type: 'spending'
      };
      
      addVoucher(newVoucher);
      showMessage(`🎉 Voucher collected: ${name}!`, 'success');
    }

    // ===== Add Voucher to Collection =====
    function addVoucher(voucher) {
      vouchers.push(voucher);
      AppData.set('vouchers', vouchers);
      renderMyVouchers();
    }

    // ===== Render User's Vouchers =====
    function renderMyVouchers() {
      const container = document.getElementById('myVoucherList');
      const empty = document.getElementById('noVoucher');
      const seeAllBtn = document.getElementById('seeAllVouchersBtn');

      if (vouchers.length === 0) {
        empty.style.display = 'block';
        container.style.display = 'none';
        if (seeAllBtn) seeAllBtn.style.display = 'none';
        return;
      }

      empty.style.display = 'none';
      container.style.display = 'grid';
      container.innerHTML = '';

      // Show only first 6 vouchers
      const displayVouchers = vouchers.slice(0, 6);

      displayVouchers.forEach((voucher, index) => {
        const voucherEl = document.createElement('div');
        voucherEl.className = 'voucher-card bounce';
        voucherEl.innerHTML = `
          <div class="voucher-name">${voucher.name}</div>
          <div class="voucher-description">${voucher.description || voucher.type}</div>
          <div class="voucher-expires">Expires: ${voucher.expires}</div>
          <button onclick="useVoucher(${index})" class="voucher-use-btn">
            Use Now
          </button>
        `;
        container.appendChild(voucherEl);

        // Remove bounce animation after it completes
        setTimeout(() => voucherEl.classList.remove('bounce'), 1000);
      });

      // Show/hide "See All" button
      if (seeAllBtn) {
        if (vouchers.length > 6) {
          seeAllBtn.style.display = 'block';
        } else {
          seeAllBtn.style.display = 'none';
        }
      }
    }

    // ===== Use Voucher =====
    function useVoucher(index) {
      // Check authentication before using voucher
      if (window.requireAuth && !window.requireAuth('use-voucher', function() {
        performVoucherUse(index);
      })) return;
      
      // If no requireAuth function, use voucher normally (for logged-in users)
      if (!window.requireAuth) {
        performVoucherUse(index);
      }
    }
    
    function performVoucherUse(index) {
      if (vouchers[index]) {
        const voucher = vouchers[index];
        AppData.set('selected_voucher', voucher);
        window.location.href = 'payment.html';
      }
    }

    // ===== Achievements System =====
    function renderAchievements() {
      const container = document.getElementById('achievementGrid');
      const achievements = [
        { id: 'first_checkin', name: '🌟 First Steps', desc: 'Complete your first check-in', completed: true },
        { id: 'streak_3', name: '🔥 On Fire!', desc: 'Check-in 3 days in a row', completed: checkinStreak >= 3 },
        { id: 'big_spender', name: '💎 Big Spender', desc: 'Spend RM500 in total', completed: false },
        { id: 'voucher_collector', name: '🎫 Collector', desc: 'Collect 5 vouchers', completed: vouchers.length >= 5 },
        { id: 'points_master', name: '⭐ Points Master', desc: 'Accumulate 1000 points', completed: points >= 1000 },
        { id: 'loyal_customer', name: '👑 VIP Status', desc: 'Reach Gold membership', completed: false }
      ];
      
      container.innerHTML = '';
      achievements.forEach(achievement => {
        const item = document.createElement('div');
        item.className = `achievement-item ${achievement.completed ? 'completed' : ''}`;
        
        item.innerHTML = `
          <div class="achievement-title ${achievement.completed ? 'completed' : ''}">${achievement.name}</div>
          <div class="achievement-desc">${achievement.desc}</div>
          <div class="achievement-status ${achievement.completed ? 'completed' : ''}">
            ${achievement.completed ? '✅ Completed!' : '🔒 Locked'}
          </div>
        `;
        
        container.appendChild(item);
      });
    }

    // ===== Utility Functions =====
    function updatePointsDisplay() {
      // Update points display for both guests and authenticated users
      const pointsEl = document.getElementById('points');
      if (pointsEl) {
        pointsEl.textContent = rewardsData.isAuthenticated
          ? new Intl.NumberFormat().format(points)
          : points;
      }
    }

    function getExpiryDate(days) {
      const date = new Date();
      date.setDate(date.getDate() + days);
      return date.toLocaleDateString();
    }

    function showConfirm(title, message, onConfirm, onCancel = null) {
      // Create confirmation modal
      const overlay = document.createElement('div');
      overlay.className = 'confirmation-overlay';

      overlay.innerHTML = `
        <div class="confirmation-modal">
          <div class="confirmation-title">${title}</div>
          <div class="confirmation-message">${message}</div>
          <div class="confirmation-buttons">
            <button class="confirmation-btn cancel">Cancel</button>
            <button class="confirmation-btn confirm">Confirm</button>
          </div>
        </div>
      `;

      document.body.appendChild(overlay);

      // Handle button clicks
      const cancelBtn = overlay.querySelector('.cancel');
      const confirmBtn = overlay.querySelector('.confirm');

      const cleanup = () => {
        if (document.body.contains(overlay)) {
          document.body.removeChild(overlay);
        }
      };

      cancelBtn.addEventListener('click', () => {
        cleanup();
        if (onCancel) onCancel();
      });

      confirmBtn.addEventListener('click', () => {
        cleanup();
        if (onConfirm) onConfirm();
      });

      // Close on overlay click
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          cleanup();
          if (onCancel) onCancel();
        }
      });
    }

    function showRewardQR(redemptionId, rewardName, redemptionCode) {
      // Use the actual redemption code from database
      const qrContent = redemptionCode;

      // Create QR modal
      const overlay = document.createElement('div');
      overlay.className = 'confirmation-overlay';
      overlay.innerHTML = `
        <div class="confirmation-modal" style="max-width: 400px;">
          <div class="confirmation-title">🎁 Show to Staff</div>
          <div class="confirmation-message" style="text-align: center;">
            <h3 style="margin: 16px 0; color: var(--brand);">${rewardName}</h3>
            <div style="background: white; padding: 20px; border-radius: 12px; margin: 16px 0; border: 2px solid var(--muted);">
              <div style="font-family: monospace; font-size: 18px; font-weight: bold; color: var(--text); word-break: break-all; line-height: 1.4;">
                ${qrContent}
              </div>
            </div>
            <p style="color: var(--text-2); font-size: 0.9rem; margin-top: 16px;">
              📱 Show this code to staff to redeem your reward
            </p>
            <p style="color: var(--text-3); font-size: 0.8rem; margin-top: 8px;">
              Redemption ID: #${redemptionId}
            </p>
          </div>
          <div class="confirmation-buttons">
            <button class="confirmation-btn confirm" style="width: 100%;">Close</button>
          </div>
        </div>
      `;

      document.body.appendChild(overlay);

      // Handle close
      const confirmBtn = overlay.querySelector('.confirm');
      const cleanup = () => {
        if (document.body.contains(overlay)) {
          document.body.removeChild(overlay);
        }
      };

      confirmBtn.addEventListener('click', cleanup);
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) cleanup();
      });
    }

    function showMessage(message, type = 'info') {
      // Create toast notification
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      toast.textContent = message;
      
      document.body.appendChild(toast);
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }

    // ===== Search Functionality =====
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      const clearBtn = document.getElementById('clearSearch');
      
      searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        clearBtn.style.display = query ? 'block' : 'none';
        
        // Search through rewards and vouchers
        const allItems = document.querySelectorAll('.reward-item, .voucher-card, .achievement-item');
        allItems.forEach(item => {
          const text = item.textContent.toLowerCase();
          item.style.display = text.includes(query) ? 'flex' : 'none';
        });
      });
      
      clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        clearBtn.style.display = 'none';
        const allItems = document.querySelectorAll('.reward-item, .voucher-card, .achievement-item');
        allItems.forEach(item => item.style.display = 'flex');
      });
    }

    // ===== Add Demo Vouchers =====
    function addDemoVouchers() {
        if (AppData.get('vouchers', []).length > 0) return; // Don't add if already exist
        const demoVouchers = [
            {
            name: '🎉 Welcome Bonus',
            description: 'New member special discount',
            expires: getExpiryDate(15),
            type: 'welcome'
            },
            {
            name: '🍕 Weekend Special',
            description: '20% off on weekends',
            expires: getExpiryDate(7),
            type: 'weekend'
            }
        ];
      
        vouchers.push(...demoVouchers);
        AppData.set('vouchers', vouchers);
    }

    // ===== Update Loyalty Progress =====
    function updateLoyaltyProgress() {
      // Progress is now handled server-side through userTierInfo
      // This function can be used for any dynamic updates if needed
    }

    // ===== Interactive Effects =====
    function addInteractiveEffects() {
      // Add click effects to cards
      document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function(e) {
          // Only if clicking the card itself, not buttons
          if (e.target === this || (!e.target.closest('button') && !e.target.closest('.reward-item') && !e.target.closest('.voucher-card'))) {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
              this.style.transform = 'translateY(-8px)';
            }, 100);
          }
        });
      });

      // Add floating effect to points display
      setTimeout(() => {
        const pointsDisplay = document.querySelector('.points-display');
        if (pointsDisplay) pointsDisplay.classList.add('float');
      }, 2000);
    }

    // ===== Initialize Everything =====
    function initialize() {
      renderPageContent();
      updatePointsDisplay();
      initializeCheckin();
      setupCheckin();
      renderPointsRewards();
      renderVoucherCollection();
      addDemoVouchers();
      renderMyVouchers();
      renderAchievements();
      updateLoyaltyProgress();
      setupSearch();
      addInteractiveEffects();
      renderSpecialEvents();
      
      // Welcome message
      setTimeout(() => {
        showMessage('🎉 Welcome to SmartDine Rewards! Start earning points today!', 'success');
      }, 1000);
    }

    // ===== Notification Bell Functionality =====
    function initializeNotifications() {
      const notificationBell = document.getElementById('notificationBell');
      const notificationBadge = document.getElementById('notificationBadge');

      if (notificationBell) {
        notificationBell.addEventListener('click', function() {

          // Rewards-specific notifications
          const notifications = [
            'You earned 50 points from your last order!',
            'New reward available: Free appetizer',
            'Your loyalty tier has been upgraded to Silver!'
          ];

          // Create a simple notification popup
          const popup = document.createElement('div');
          popup.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-width: 400px;
            width: 90%;
          `;

          popup.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
              <h3 style="margin: 0; color: #1e293b;">Reward Notifications</h3>
              <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
            </div>
            <div>
              ${notifications.map(notif => `
                <div style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">
                  ${notif}
                </div>
              `).join('')}
            </div>
            <div style="text-align: center; margin-top: 15px;">
              <button onclick="this.parentElement.parentElement.remove()" style="background: #6366f1; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">Close</button>
            </div>
          `;

          // Add backdrop
          const backdrop = document.createElement('div');
          backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
          `;
          backdrop.onclick = () => {
            backdrop.remove();
            popup.remove();
          };

          document.body.appendChild(backdrop);
          document.body.appendChild(popup);
        });
      }
    }

    // ===== Error Handling for Loyalty Tiers =====
    // Clear any old loyalty tier data that might cause conflicts
    try {
      // Remove any cached data that might conflict with new structure
      if (localStorage.getItem('loyaltyTiers')) {
        localStorage.removeItem('loyaltyTiers');
      }
    } catch (e) {
      console.log('Cleared legacy data');
    }

    // ===== Start the Application =====
    document.addEventListener('DOMContentLoaded', function() {
      try {
        initialize();
        initializeNotifications();
      } catch (error) {
        console.log('Initialization error (non-critical):', error);
        // Continue with basic functionality even if some parts fail
      }
    });
    function redeemReward(rewardId, pointsRequired) {
      const user = rewardsData.user;

      if (!user) {
        showMessage('Please login to redeem rewards', 'warning');
        return;
      }

      const userPoints = user.points_balance || 0;

      if (userPoints < pointsRequired) {
        showMessage(`Insufficient points! You need ${pointsRequired} points but only have ${userPoints} points.`, 'error');
        return;
      }

      showConfirm(
        '🎁 Redeem Reward',
        `Are you sure you want to redeem this reward for ${pointsRequired} points?`,
        () => {
          // Confirmed - proceed with redemption
          fetch(rewardsData.redeemRoute, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': rewardsData.csrfToken
            },
            body: JSON.stringify({ exchange_point_id: rewardId })
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              showMessage(data.message, 'success');

              // Update points balance in UI
              points = data.new_balance;
              updatePointsDisplay();

              // Reload page to refresh usage limits and reward availability
              setTimeout(() => {
                window.location.reload();
              }, 1500); // Small delay to show success message
            } else {
              showMessage(data.message || 'Failed to redeem reward', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while redeeming the reward', 'error');
          });
        }
      );
    }

    function showAllExchangePoints() {
      document.getElementById('exchangePointsModal').style.display = 'block';
    }

    function closeExchangePointsModal() {
      document.getElementById('exchangePointsModal').style.display = 'none';
    }

    function openMyRewardsModal() {
      document.getElementById('myRewardsModal').style.display = 'block';
    }

    function closeMyRewardsModal() {
      document.getElementById('myRewardsModal').style.display = 'none';
    }

    // ===== All Vouchers Modal Functions =====
    function showAllVouchersModal() {
      const modal = document.getElementById('allVouchersModal');
      const modalBody = modal.querySelector('.modal-body .voucher-grid');

      // Clear existing content
      modalBody.innerHTML = '';

      // Render all vouchers in modal
      vouchers.forEach((voucher, index) => {
        const voucherEl = document.createElement('div');
        voucherEl.className = 'voucher-card';
        voucherEl.innerHTML = `
          <div class="voucher-name">${voucher.name}</div>
          <div class="voucher-description">${voucher.description || voucher.type}</div>
          <div class="voucher-expires">Expires: ${voucher.expires}</div>
          <button onclick="useVoucherFromModal(${index})" class="voucher-use-btn">
            Use Now
          </button>
        `;
        modalBody.appendChild(voucherEl);
      });

      modal.style.display = 'block';
    }

    function closeAllVouchersModal() {
      document.getElementById('allVouchersModal').style.display = 'none';
    }

    function useVoucherFromModal(index) {
      // Close modal first
      closeAllVouchersModal();
      // Then use the voucher
      useVoucher(index);
    }

    // Make functions globally available
    window.showAllVouchersModal = showAllVouchersModal;
    window.closeAllVouchersModal = closeAllVouchersModal;
    window.useVoucherFromModal = useVoucherFromModal;

    // Close modal when clicking outside
    window.onclick = function(event) {
      const exchangeModal = document.getElementById('exchangePointsModal');
      const myRewardsModal = document.getElementById('myRewardsModal');
      const allVouchersModal = document.getElementById('allVouchersModal');

      if (event.target == exchangeModal) {
        exchangeModal.style.display = 'none';
      }

      if (event.target == myRewardsModal) {
        myRewardsModal.style.display = 'none';
      }

      if (event.target == allVouchersModal) {
        allVouchersModal.style.display = 'none';
      }
    }