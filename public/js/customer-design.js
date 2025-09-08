/**
 * The Stag Customer Design System - JavaScript Components
 * Interactive functionality for customer-facing pages
 */

// ===== Configuration & Constants =====
const STORAGE_KEY = 'THE_STAG_HOMEPAGE_DATA';
const FEEDBACK_KEY = 'smartdine_feedback_v1';

// ===== Core Application Class =====
class TheStagApp {
    constructor() {
        this.isLoaded = false;
        this.observers = new Map();
        
        // Bind methods to this context
        this.init = this.init.bind(this);
        this.loadDynamicContent = this.loadDynamicContent.bind(this);
        this.initializeNavigation = this.initializeNavigation.bind(this);
        this.initializeFeedbackForm = this.initializeFeedbackForm.bind(this);
        this.initializeScrollFeatures = this.initializeScrollFeatures.bind(this);
        this.initializeAnimations = this.initializeAnimations.bind(this);
    }

    // ===== Initialization =====
    async init() {
        if (this.isLoaded) return;
        
        try {
            // Load dynamic content first if available
            await this.loadDynamicContent();
            
            // Initialize all interactive components
            this.initializeNavigation();
            this.initializeFeedbackForm();
            this.initializeScrollFeatures();
            this.initializeAnimations();
            this.updateYear();
            
            this.isLoaded = true;
            console.log('🦌 The Stag App initialized successfully');
        } catch (error) {
            console.error('Failed to initialize The Stag App:', error);
        }
    }

    // ===== Dynamic Content Management =====
    async loadDynamicContent() {
        let data = null;
        const storedData = localStorage.getItem(STORAGE_KEY);

        if (storedData) {
            console.log("📦 Loading content from localStorage (live preview)");
            try {
                data = JSON.parse(storedData);
            } catch (error) {
                console.warn("Failed to parse stored data:", error);
            }
        } else {
            console.log("🔍 No live preview data found. Loading from data.json if available.");
            try {
                const response = await fetch('/shared/data.json');
                if (response.ok) {
                    data = await response.json();
                }
            } catch (error) {
                console.info("No data.json found - using default content");
            }
        }

        if (data) {
            this.populateHero(data.hero);
            this.populateAbout(data.about);
            this.populatePromotions(data.promotions);
            this.populateStats(data.stats);
            this.populateContact(data.contact);
        }
    }

    // ===== Content Population Methods =====
    populateHero(data) {
        if (!data) return;
        
        const heroTitle = document.getElementById('heroTitle');
        if (heroTitle && data.title) {
            heroTitle.innerHTML = data.title.replace(/\{(.*?)}/g, '<span>$1</span>');
        }
        
        this.updateElementText('heroSubtitle', data.subtitle);
        this.updateElementText('heroBtn1', data.btn1Text);
        this.updateElementText('heroBtn2', data.btn2Text);
    }

    populateAbout(data) {
        if (!data) return;
        
        this.updateElementText('aboutTitle', data.title);
        this.updateElementText('aboutSubtitle', data.subtitle);
        this.updateElementText('aboutDescription', data.description);
        this.updateElementText('aboutBtn1', data.btn1Text);
        this.updateElementText('aboutBtn2', data.btn2Text);

        const list = document.getElementById('aboutList');
        if (list && data.feature1) {
            list.innerHTML = `
                <li>${data.feature1}</li>
                <li>${data.feature2}</li>
                <li>${data.feature3}</li>
                <li>${data.feature4}</li>
            `;
        }
    }

    populatePromotions(data) {
        if (!data) return;
        
        const grid = document.getElementById('promotionGrid');
        if (!grid) return;

        // Handle both data structures: { items: [...] } from admin and [...] from simple data.json
        const promotions = Array.isArray(data.items) ? data.items : (Array.isArray(data) ? data : []);

        this.updateElementText('promotionTitle', data.title);
        this.updateElementText('promotionSubtitle', data.subtitle);

        if (promotions.length === 0) return;

        // Clear existing promotional cards and populate with new data
        const dynamicCards = grid.querySelectorAll('[data-dynamic="true"]');
        dynamicCards.forEach(card => card.remove());

        promotions.forEach((promo, index) => {
            const card = this.createPromotionCard(promo, index);
            grid.appendChild(card);
        });
    }

    populateStats(data) {
        if (!data) return;
        
        const grid = document.getElementById('statsGrid');
        if (!grid) return;

        const stats = [data.stat1, data.stat2, data.stat3, data.stat4];
        const cards = grid.querySelectorAll('.stat-card');
        
        stats.forEach((stat, index) => {
            if (stat && stat.value && cards[index]) {
                const card = cards[index];
                const icon = card.querySelector('.stat-icon');
                const number = card.querySelector('.stat-number');
                const label = card.querySelector('.stat-label');
                
                if (icon) icon.textContent = stat.icon;
                if (number) number.textContent = stat.value;
                if (label) label.textContent = stat.label;
            }
        });
    }

    populateContact(data) {
        if (!data) return;
        
        this.updateElementText('contactTitle', data.title);
        this.updateElementText('contactSubtitle', data.subtitle);
        this.updateElementText('feedbackTitle', data.feedbackTitle);
        this.updateElementText('feedbackSubtitle', data.feedbackSubtitle);

        const list = document.getElementById('infoList');
        if (list && data.address) {
            list.innerHTML = `
                <li><strong>📍 Address:</strong> ${data.address}</li>
                <li><strong>📞 Phone:</strong> ${data.phone}</li>
                <li><strong>🕒 Hours:</strong> ${data.hours}</li>
                <li><strong>✉️ Email:</strong> ${data.email}</li>
            `;
        }
    }

    // ===== Helper Methods =====
    updateElementText(elementId, text) {
        if (!text) return;
        const element = document.getElementById(elementId);
        if (element) element.textContent = text;
    }

    createPromotionCard(promo, index) {
        const card = document.createElement('article');
        card.className = 'card';
        card.setAttribute('role', 'listitem');
        card.setAttribute('data-dynamic', 'true');
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
            <div class="card-img" style="display:grid;place-items:center;font-size:4rem">${promo.img || '🍽️'}</div>
            <div class="card-body">
                <div class="card-title">${promo.name || 'Special Dish'}</div>
                <p class="card-description">${promo.description || 'Delicious meal prepared with care.'}</p>
                <div class="price">RM ${(promo.price || 25.00).toFixed(2)}</div>
                <div class="card-actions">
                    <a class="btn-muted" href="${promo.link || '/customer/food'}">View Details</a>
                    <a class="btn-primary" href="${promo.link || '/customer/food'}">Order Now</a>
                </div>
            </div>
        `;
        
        return card;
    }

    updateYear() {
        const yearElements = document.querySelectorAll('#year, .year');
        const currentYear = new Date().getFullYear();
        yearElements.forEach(el => el.textContent = currentYear);
    }

    // ===== Navigation System =====
    initializeNavigation() {
        // Smooth scroll for anchor links
        document.querySelectorAll('[data-scroll]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = btn.getAttribute('data-scroll');
                this.smoothScrollTo(target);
            });
        });

        // Update active nav states based on current route
        this.updateActiveNavigation();
    }

    smoothScrollTo(target) {
        const el = typeof target === 'string' ? document.querySelector(target) : target;
        if (!el) return;
        
        const targetPosition = el.getBoundingClientRect().top + window.scrollY - 80;
        window.scrollTo({ 
            top: targetPosition, 
            behavior: 'smooth' 
        });
    }

    updateActiveNavigation() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href)) {
                item.classList.add('active');
            }
        });
    }

    // ===== Feedback Form Management =====
    initializeFeedbackForm() {
        const form = document.getElementById('feedbackForm');
        const note = document.getElementById('fbNote');
        const clearBtn = document.getElementById('btnClear');

        if (!form) return;

        // Clear button functionality
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                form.reset();
                this.clearFormMessage(note);
            });
        }

        // Handle form submission for non-Laravel forms (localStorage fallback)
        if (!form.hasAttribute('action') || form.getAttribute('action') === '#') {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFeedbackSubmission(form, note);
            });
        }

        // Auto-clear success/error messages
        this.setupMessageAutoClear(note);
    }

    handleFeedbackSubmission(form, note) {
        const formData = {
            name: this.getInputValue('fbName'),
            contact: this.getInputValue('fbContact'),
            subject: this.getInputValue('fbSubject'),
            message: this.getInputValue('fbMessage'),
            timestamp: new Date().toISOString()
        };

        // Validation
        if (!formData.name || !formData.message) {
            this.showFormMessage(note, 'Please fill in your name and message.', 'error');
            return;
        }

        try {
            // Store in localStorage as fallback
            const existingFeedback = JSON.parse(localStorage.getItem(FEEDBACK_KEY) || '[]');
            existingFeedback.push(formData);
            localStorage.setItem(FEEDBACK_KEY, JSON.stringify(existingFeedback));
            
            form.reset();
            this.showFormMessage(note, 'Thank you! Your feedback has been submitted successfully.', 'success');
            
            // Auto-clear after 5 seconds
            setTimeout(() => this.clearFormMessage(note), 5000);
        } catch (error) {
            console.error('Feedback submission error:', error);
            this.showFormMessage(note, 'Sorry, there was an error submitting your feedback. Please try again.', 'error');
        }
    }

    getInputValue(elementId) {
        const element = document.getElementById(elementId);
        return element ? element.value.trim() : '';
    }

    showFormMessage(noteElement, message, type) {
        if (!noteElement) return;
        
        noteElement.textContent = message;
        noteElement.style.color = type === 'error' ? '#dc2626' : '#16a34a';
        noteElement.style.fontWeight = '600';
        
        // Smooth appear animation
        noteElement.style.opacity = '0';
        noteElement.style.transform = 'translateY(10px)';
        
        requestAnimationFrame(() => {
            noteElement.style.transition = 'all 0.3s ease';
            noteElement.style.opacity = '1';
            noteElement.style.transform = 'translateY(0)';
        });
    }

    clearFormMessage(noteElement) {
        if (!noteElement) return;
        noteElement.textContent = '';
        noteElement.style.color = '';
    }

    setupMessageAutoClear(noteElement) {
        if (!noteElement || !noteElement.textContent.trim()) return;
        
        setTimeout(() => {
            if (noteElement) {
                noteElement.style.transition = 'opacity 0.3s ease';
                noteElement.style.opacity = '0';
                setTimeout(() => {
                    noteElement.textContent = '';
                    noteElement.style.opacity = '1';
                }, 300);
            }
        }, 5000);
    }

    // ===== Scroll Features =====
    initializeScrollFeatures() {
        this.initializeBackToTop();
        this.initializeScrollSpy();
    }

    initializeBackToTop() {
        const toTopBtn = document.getElementById('toTop');
        if (!toTopBtn) return;

        let isVisible = false;

        const toggleButton = () => {
            const shouldShow = window.scrollY > 500;
            
            if (shouldShow && !isVisible) {
                toTopBtn.style.display = 'block';
                requestAnimationFrame(() => {
                    toTopBtn.style.opacity = '1';
                });
                isVisible = true;
            } else if (!shouldShow && isVisible) {
                toTopBtn.style.opacity = '0';
                setTimeout(() => {
                    if (!isVisible) toTopBtn.style.display = 'none';
                }, 300);
                isVisible = false;
            }
        };

        // Throttled scroll event
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    toggleButton();
                    ticking = false;
                });
                ticking = true;
            }
        });

        toTopBtn.addEventListener('click', () => {
            this.smoothScrollTo('#hero');
        });
    }

    initializeScrollSpy() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-item[href^="#"]');

        if (sections.length === 0 || navLinks.length === 0) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    const id = entry.target.getAttribute('id');
                    const navLink = document.querySelector(`.nav-item[href="#${id}"]`);
                    
                    if (entry.isIntersecting) {
                        navLinks.forEach(link => link.classList.remove('active'));
                        if (navLink) navLink.classList.add('active');
                    }
                });
            },
            {
                threshold: 0.3,
                rootMargin: '-50px 0px -50px 0px'
            }
        );

        sections.forEach(section => observer.observe(section));
        this.observers.set('scrollSpy', observer);
    }

    // ===== Animation System =====
    initializeAnimations() {
        this.setupIntersectionAnimations();
        this.setupHoverAnimations();
    }

    setupIntersectionAnimations() {
        const animatedElements = document.querySelectorAll('.card, .stat-card, .about-text, .feedback');
        if (animatedElements.length === 0) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            observer.observe(el);
        });

        this.observers.set('animations', observer);
    }

    setupHoverAnimations() {
        // Add enhanced hover effects for cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', this.handleCardHover);
            card.addEventListener('mouseleave', this.handleCardLeave);
        });
    }

    handleCardHover(e) {
        const card = e.currentTarget;
        const img = card.querySelector('.card-img');
        if (img) {
            img.style.transform = 'scale(1.05)';
        }
    }

    handleCardLeave(e) {
        const card = e.currentTarget;
        const img = card.querySelector('.card-img');
        if (img) {
            img.style.transform = 'scale(1)';
        }
    }

    // ===== Cleanup Methods =====
    destroy() {
        // Clean up observers
        this.observers.forEach(observer => {
            if (observer && observer.disconnect) {
                observer.disconnect();
            }
        });
        this.observers.clear();
        
        // Remove event listeners
        window.removeEventListener('scroll', this.handleScroll);
        
        this.isLoaded = false;
        console.log('🦌 The Stag App destroyed');
    }
}

// ===== Global Application Instance =====
window.TheStagApp = TheStagApp;

// ===== Auto-Initialize on DOM Ready =====
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.theStagApp = new TheStagApp();
        window.theStagApp.init();
    });
} else {
    // DOM is already loaded
    window.theStagApp = new TheStagApp();
    window.theStagApp.init();
}

// ===== Utility Functions (Global) =====
window.smoothTo = function(target) {
    if (window.theStagApp) {
        window.theStagApp.smoothScrollTo(target);
    }
};

// ===== Export for module systems =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TheStagApp;
}