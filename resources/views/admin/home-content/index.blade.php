@extends('layouts.admin')

@section('title', 'Homepage Content Management')
@section('page-title', 'Homepage Content')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/home-content.css') }}">
@endsection

@section('content')
<!-- Tabs for different sections -->
<div class="tabs">
  <div class="tab active" data-tab="hero">Hero Section</div>
  <div class="tab" data-tab="about">About Section</div>
  <div class="tab" data-tab="specials">Promotions</div>
  <div class="tab" data-tab="stats">Statistics</div>
  <div class="tab" data-tab="contact">Contact Info</div>
</div>

<!-- Hero Section Tab -->
<div class="tab-content active" id="hero-tab">
  <div class="admin-section">
    <div class="section-header">
      <h2 class="section-title">Hero Section Content</h2>
    </div>

    <form id="heroForm" novalidate>
      <div class="form-grid">
        <div class="form-group form-full">
          <label class="form-label" for="heroTitle">Main Title</label>
          <input type="text" class="form-input" id="heroTitle" placeholder="Welcome to The Stag" value="Welcome to The Stag" required>
          <div class="form-error" id="heroTitle-error">Please enter a main title</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="heroHighlight">Highlighted Text</label>
          <input type="text" class="form-input" id="heroHighlight" placeholder="The Stag" required>
          <div class="form-error" id="heroHighlight-error">Please enter highlighted text</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="heroSubtitle">Subtitle</label>
          <textarea class="form-textarea" id="heroSubtitle" placeholder="Enter subtitle text" required>Experience premium dining with our signature steaks, authentic Malaysian flavors, and exceptional service that creates unforgettable culinary memories</textarea>
          <div class="form-error" id="heroSubtitle-error">Please enter a subtitle</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="heroBtn1Text">Primary Button Text</label>
          <input type="text" class="form-input" id="heroBtn1Text" value="Explore Menu" required>
          <div class="form-error" id="heroBtn1Text-error">Please enter button text</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="heroBtn2Text">Secondary Button Text</label>
          <input type="text" class="form-input" id="heroBtn2Text" value="Learn More" required>
          <div class="form-error" id="heroBtn2Text-error">Please enter button text</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label">Background Gradient</label>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="heroBg1">Color 1</label>
              <input type="color" class="form-input" id="heroBg1" value="#6366f1" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="heroBg2">Color 2</label>
              <input type="color" class="form-input" id="heroBg2" value="#5856eb" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="heroBg3">Color 3</label>
              <input type="color" class="form-input" id="heroBg3" value="#ff6b35" required>
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="admin-btn btn-secondary" id="resetHeroBtn">Reset</button>
        <button type="submit" class="admin-btn btn-primary">
          Save Changes
          <span class="spinner" style="display: none;"></span>
        </button>
      </div>
    </form>

    <div class="preview-container">
      <div class="preview-title">Preview</div>
      <div style="
        background: linear-gradient(135deg, #6366f1, #5856eb, #ff6b35);
        padding: 20px;
        border-radius: 8px;
        color: white;
        text-align: center;
      ">
        <h2 style="font-size: 24px; margin-bottom: 10px;">Welcome to <span style="background: linear-gradient(90deg, #ffd700, #ffa500); -webkit-background-clip: text; background-clip: text; color: transparent;">The Stag</span></h2>
        <p style="opacity: 0.9;">Experience premium dining with our signature steaks, authentic Malaysian flavors, and exceptional service that creates unforgettable culinary memories</p>
        <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
          <button style="padding: 8px 16px; background: white; border: none; border-radius: 8px; font-weight: bold;">Explore Menu</button>
          <button style="padding: 8px 16px; background: transparent; border: 1px solid white; border-radius: 8px; color: white;">Learn More</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- About Section Tab -->
<div class="tab-content" id="about-tab">
  <div class="admin-section">
    <div class="section-header">
      <h2 class="section-title">About Section Content</h2>
    </div>

    <form id="aboutForm" novalidate>
      <div class="form-grid">
        <div class="form-group form-full">
          <label class="form-label" for="aboutTitle">Section Title</label>
          <input type="text" class="form-input" id="aboutTitle" value="About The Stag" required>
          <div class="form-error" id="aboutTitle-error">Please enter a section title</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="aboutSubtitle">Subtitle</label>
          <input type="text" class="form-input" id="aboutSubtitle" value="Discover the perfect blend of Western cuisine and Malaysian favorites in the heart of the city." required>
          <div class="form-error" id="aboutSubtitle-error">Please enter a subtitle</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="aboutDescription">Description</label>
          <textarea class="form-textarea" id="aboutDescription" required>At The Stag, we pride ourselves on delivering exceptional dining experiences through carefully crafted dishes, premium ingredients, and warm hospitality. From our signature steaks to authentic local delicacies, every meal tells a story.</textarea>
          <div class="form-error" id="aboutDescription-error">Please enter a description</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label">Features List</label>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="aboutFeature1">Feature 1</label>
              <input type="text" class="form-input" id="aboutFeature1" value="Premium beef steaks aged to perfection" required>
              <div class="form-error" id="aboutFeature1-error">Please enter a feature</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="aboutFeature2">Feature 2</label>
              <input type="text" class="form-input" id="aboutFeature2" value="Authentic Malaysian dishes with a modern twist" required>
              <div class="form-error" id="aboutFeature2-error">Please enter a feature</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="aboutFeature3">Feature 3</label>
              <input type="text" class="form-input" id="aboutFeature3" value="Fresh pasta made daily with imported Italian ingredients" required>
              <div class="form-error" id="aboutFeature3-error">Please enter a feature</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="aboutFeature4">Feature 4</label>
              <input type="text" class="form-input" id="aboutFeature4" value="Award-winning culinary team with years of expertise" required>
              <div class="form-error" id="aboutFeature4-error">Please enter a feature</div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="aboutBtn1Text">Primary Button Text</label>
          <input type="text" class="form-input" id="aboutBtn1Text" value="View Full Menu" required>
          <div class="form-error" id="aboutBtn1Text-error">Please enter button text</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="aboutBtn2Text">Secondary Button Text</label>
          <input type="text" class="form-input" id="aboutBtn2Text" value="Contact Us" required>
          <div class="form-error" id="aboutBtn2Text-error">Please enter button text</div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="admin-btn btn-secondary" id="resetAboutBtn">Reset</button>
        <button type="submit" class="admin-btn btn-primary">
          Save Changes
          <span class="spinner" style="display: none;"></span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Specials Tab -->
<div class="tab-content" id="specials-tab">
  <div class="admin-section">
    <div class="section-header">
      <h2 class="section-title">Promotions Management</h2>
      <button class="admin-btn btn-primary" id="addSpecialBtn" aria-label="Add new promotion">
        <div class="admin-nav-icon" aria-hidden="true"><i class="fas fa-plus"></i></div>
        Add Promotion
      </button>
    </div>

    <form id="specialsForm" novalidate>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="specialsTitle">Section Title</label>
          <input type="text" class="form-input" id="specialsTitle" value="Featured Promotions" required>
          <div class="form-error" id="specialsTitle-error">Please enter a section title</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="specialsSubtitle">Section Subtitle</label>
          <input type="text" class="form-input" id="specialsSubtitle" value="Don't miss out on our limited-time offers and special deals!" required>
          <div class="form-error" id="specialsSubtitle-error">Please enter a section subtitle</div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="admin-btn btn-secondary" id="resetSpecialsBtn">Reset</button>
        <button type="submit" class="admin-btn btn-primary">
          Save Changes
          <span class="spinner" style="display: none;"></span>
        </button>
      </div>
    </form>

    <div class="specials-grid" id="specialsContainer">
      <!-- Specials will be loaded here -->
    </div>

    <div class="empty-state" id="specialsEmptyState" style="display: none;">
      <div class="empty-state-icon" aria-hidden="true">🍽️</div>
      <div class="empty-state-title">No promotions yet</div>
      <div class="empty-state-text">Add your first promotion to get started</div>
    </div>
  </div>
</div>

<!-- Stats Tab -->
<div class="tab-content" id="stats-tab">
  <div class="admin-section">
    <div class="section-header">
      <h2 class="section-title">Statistics Section</h2>
    </div>

    <form id="statsForm" novalidate>
      <div class="stats-grid">
        <div class="form-group">
          <label class="form-label" for="stat1Icon">Stat 1 - Icon</label>
          <input type="text" class="form-input" id="stat1Icon" value="🍽️" required>
          <div class="form-error" id="stat1Icon-error">Please enter an icon</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat1Value">Stat 1 - Value</label>
          <input type="text" class="form-input" id="stat1Value" value="100+" required>
          <div class="form-error" id="stat1Value-error">Please enter a value</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat1Label">Stat 1 - Label</label>
          <input type="text" class="form-input" id="stat1Label" value="Menu Items" required>
          <div class="form-error" id="stat1Label-error">Please enter a label</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="stat2Icon">Stat 2 - Icon</label>
          <input type="text" class="form-input" id="stat2Icon" value="⭐" required>
          <div class="form-error" id="stat2Icon-error">Please enter an icon</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat2Value">Stat 2 - Value</label>
          <input type="text" class="form-input" id="stat2Value" value="4.8" required>
          <div class="form-error" id="stat2Value-error">Please enter a value</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat2Label">Stat 2 - Label</label>
          <input type="text" class="form-input" id="stat2Label" value="Customer Rating" required>
          <div class="form-error" id="stat2Label-error">Please enter a label</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="stat3Icon">Stat 3 - Icon</label>
          <input type="text" class="form-input" id="stat3Icon" value="👨‍🍳" required>
          <div class="form-error" id="stat3Icon-error">Please enter an icon</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat3Value">Stat 3 - Value</label>
          <input type="text" class="form-input" id="stat3Value" value="15+" required>
          <div class="form-error" id="stat3Value-error">Please enter a value</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat3Label">Stat 3 - Label</label>
          <input type="text" class="form-input" id="stat3Label" value="Years Experience" required>
          <div class="form-error" id="stat3Label-error">Please enter a label</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="stat4Icon">Stat 4 - Icon</label>
          <input type="text" class="form-input" id="stat4Icon" value="🏆" required>
          <div class="form-error" id="stat4Icon-error">Please enter an icon</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat4Value">Stat 4 - Value</label>
          <input type="text" class="form-input" id="stat4Value" value="25k+" required>
          <div class="form-error" id="stat4Value-error">Please enter a value</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="stat4Label">Stat 4 - Label</label>
          <input type="text" class="form-input" id="stat4Label" value="Happy Customers" required>
          <div class="form-error" id="stat4Label-error">Please enter a label</div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="admin-btn btn-secondary" id="resetStatsBtn">Reset</button>
        <button type="submit" class="admin-btn btn-primary">
          Save Changes
          <span class="spinner" style="display: none;"></span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Contact Tab -->
<div class="tab-content" id="contact-tab">
  <div class="admin-section">
    <div class="section-header">
      <h2 class="section-title">Contact Information</h2>
    </div>

    <form id="contactForm" novalidate>
      <div class="form-grid">
        <div class="form-group form-full">
          <label class="form-label" for="contactTitle">Section Title</label>
          <input type="text" class="form-input" id="contactTitle" value="Visit Us Today" required>
          <div class="form-error" id="contactTitle-error">Please enter a section title</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="contactSubtitle">Section Subtitle</label>
          <input type="text" class="form-input" id="contactSubtitle" value="We're located in the heart of the city, ready to serve you exceptional dining experiences." required>
          <div class="form-error" id="contactSubtitle-error">Please enter a section subtitle</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactAddress">Address</label>
          <input type="text" class="form-input" id="contactAddress" value="123 Food Street, City Center" required>
          <div class="form-error" id="contactAddress-error">Please enter an address</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactPhone">Phone</label>
          <input type="tel" class="form-input" id="contactPhone" value="+60 12-345-6789" required pattern="[\+]?[0-9\s\-\(\)]+">
          <div class="form-error" id="contactPhone-error">Please enter a valid phone number</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactHours">Hours</label>
          <input type="text" class="form-input" id="contactHours" value="Daily 11AM - 11PM" required>
          <div class="form-error" id="contactHours-error">Please enter operating hours</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactEmail">Email</label>
          <input type="email" class="form-input" id="contactEmail" value="hello@thestag.com" required>
          <div class="form-error" id="contactEmail-error">Please enter a valid email address</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="feedbackTitle">Feedback Form Title</label>
          <input type="text" class="form-input" id="feedbackTitle" value="Share Your Feedback" required>
          <div class="form-error" id="feedbackTitle-error">Please enter a feedback form title</div>
        </div>

        <div class="form-group form-full">
          <label class="form-label" for="feedbackSubtitle">Feedback Form Subtitle</label>
          <input type="text" class="form-input" id="feedbackSubtitle" value="Help us improve by sharing your dining experience with us." required>
          <div class="form-error" id="feedbackSubtitle-error">Please enter a feedback form subtitle</div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="admin-btn btn-secondary" id="resetContactBtn">Reset</button>
        <button type="submit" class="admin-btn btn-primary">
          Save Changes
          <span class="spinner" style="display: none;"></span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Add/Edit Special Modal -->
<div class="modal-overlay" id="specialModal" role="dialog" aria-labelledby="specialModalTitle" aria-hidden="true">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title" id="specialModalTitle">Add New Promotion</h3>
      <button class="modal-close" data-dismiss="modal" aria-label="Close">×</button>
    </div>
    <div class="modal-body">
      <form id="specialForm" novalidate>
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="specialName">Name</label>
            <input type="text" id="specialName" class="form-input" placeholder="Beef Steak (235g++)" required>
            <div class="form-error" id="specialName-error">Please enter a promotion name</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="specialPrice">Price (RM)</label>
            <input type="number" id="specialPrice" class="form-input" step="0.01" min="0" placeholder="45.00" required>
            <div class="form-error" id="specialPrice-error">Please enter a valid price</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="specialEmoji">Emoji Icon</label>
            <input type="text" id="specialEmoji" class="form-input" placeholder="🥩" maxlength="2">
            <div class="form-error" id="specialEmoji-error">Please enter an emoji (max 2 characters)</div>
          </div>
          <div class="form-group">
            <label class="form-label" for="specialLink">Link</label>
            <input type="text" id="specialLink" class="form-input" value="food.html" required>
            <div class="form-error" id="specialLink-error">Please enter a link</div>
          </div>

          <div class="form-group form-full">
            <label class="form-label" for="specialDescription">Description</label>
            <textarea id="specialDescription" class="form-textarea" placeholder="Premium beef steak grilled to perfection" required></textarea>
            <div class="form-error" id="specialDescription-error">Please enter a description</div>
          </div>
        </div>
        <input type="hidden" id="specialEditId">
      </form>
    </div>
    <div class="modal-footer">
      <button class="admin-btn btn-secondary" data-dismiss="modal">Cancel</button>
      <button class="admin-btn btn-primary" type="submit" form="specialForm">
        Save Promotion
        <span class="spinner" style="display: none;"></span>
      </button>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal-overlay" id="confirm-modal" role="dialog" aria-labelledby="confirm-modal-title" aria-hidden="true">
  <div class="modal" style="max-width: 400px;">
    <div class="modal-header">
      <h2 class="modal-title" id="confirm-modal-title">Confirm Action</h2>
      <button class="modal-close" id="confirm-modal-close-btn" aria-label="Close">×</button>
    </div>
    <div class="modal-body">
      <p id="confirm-modal-text">Are you sure you want to proceed?</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="admin-btn btn-secondary" id="confirm-modal-cancel-btn">Cancel</button>
      <button type="button" class="admin-btn btn-primary" id="confirm-modal-confirm-btn">Confirm</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/order-management.js') }}"></script>
  <script type="application/json" id="dashboard-data">
    {
      "totalCustomers": @json($totalCustomers ?? 0),
      "totalOrders": @json($totalOrders ?? 0),
      "totalRevenue": @json($totalRevenue ?? 0),
      "todayOrders": @json($todayOrders ?? 0)
    }
  </script>

  <script>
    // ===== DATA MANAGEMENT =====
    const STORAGE_KEY = 'THE_STAG_HOMEPAGE_DATA';
    const BOOKING_STORAGE_KEY = 'THE_STAG_BOOKINGS';
    let BOOKINGS = [];
    let SPECIALS_DATA = [];

    // ===== UI SERVICE =====
    const uiService = {
      showLoading: function() {
        // Loading handled by layout
      },

      hideLoading: function() {
        // Loading handled by layout
      },

      showToast: function(message, type = 'success', title = '') {
        // Use global toast if available
        if (typeof window.showToast === 'function') {
          window.showToast(message, type);
        }
      },

      showFormError: function(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId}-error`);

        if (field && errorElement) {
          field.classList.add('error');
          errorElement.textContent = message;
          errorElement.style.display = 'block';
        }
      },

      clearFormError: function(fieldId) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId}-error`);

        if (field && errorElement) {
          field.classList.remove('error');
          errorElement.style.display = 'none';
        }
      },

      clearAllFormErrors: function(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const fields = form.querySelectorAll('.form-input, .form-select, .form-textarea');
        fields.forEach(field => {
          field.classList.remove('error');
          const errorElement = document.getElementById(`${field.id}-error`);
          if (errorElement) {
            errorElement.style.display = 'none';
          }
        });
      },

      toggleButtonLoading: function(button, isLoading) {
        const spinner = button.querySelector('.spinner');
        if (isLoading) {
          button.disabled = true;
          if (spinner) spinner.style.display = 'inline-block';
        } else {
          button.disabled = false;
          if (spinner) spinner.style.display = 'none';
        }
      }
    };

    // ===== DATA SERVICE =====
    const dataService = {
      getStoredData: function() {
        // Get dashboard stats from PHP database
        const dashboardData = JSON.parse(document.getElementById('dashboard-data').textContent);

        // Get homepage content from PHP database
        const heroSection = @json($heroSection ?? null);
        const aboutSection = @json($aboutSection ?? null);
        const statsSection = @json($statsSection ?? null);
        const contactSection = @json($contactSection ?? null);
        const promotionSections = @json($promotionSections ?? []);

        return {
          hero: heroSection ? {
            title: heroSection.title || '',
            highlight: heroSection.highlighted_text || '',
            subtitle: heroSection.subtitle || '',
            btn1Text: heroSection.primary_button_text || '',
            btn2Text: heroSection.secondary_button_text || '',
            bg1: heroSection.background_color_1 || '',
            bg2: heroSection.background_color_2 || '',
            bg3: heroSection.background_color_3 || ''
          } : {},
          about: aboutSection ? {
            title: aboutSection.title || '',
            subtitle: aboutSection.subtitle || '',
            description: aboutSection.description || '',
            feature1: aboutSection.feature_1 || '',
            feature2: aboutSection.feature_2 || '',
            feature3: aboutSection.feature_3 || '',
            feature4: aboutSection.feature_4 || '',
            btn1Text: aboutSection.about_primary_button_text || '',
            btn2Text: aboutSection.about_secondary_button_text || ''
          } : {},
          promotions: {
            title: promotionSections.length > 0 ? promotionSections[0].title : '',
            subtitle: promotionSections.length > 0 ? promotionSections[0].subtitle : '',
            items: promotionSections.map(section => ({
              id: section.id,
              name: section.title || '',
              price: section.minimum_order_amount || 0,
              img: section.content || '',
              link: section.button_link || '',
              description: section.description || ''
            }))
          },
          stats: statsSection ? {
            stat1: {
              icon: statsSection.stat1_icon || '',
              value: statsSection.stat1_value || '',
              label: statsSection.stat1_label || ''
            },
            stat2: {
              icon: statsSection.stat2_icon || '',
              value: statsSection.stat2_value || '',
              label: statsSection.stat2_label || ''
            },
            stat3: {
              icon: statsSection.stat3_icon || '',
              value: statsSection.stat3_value || '',
              label: statsSection.stat3_label || ''
            },
            stat4: {
              icon: statsSection.stat4_icon || '',
              value: statsSection.stat4_value || '',
              label: statsSection.stat4_label || ''
            },
            totalCustomers: dashboardData.totalCustomers,
            totalOrders: dashboardData.totalOrders,
            totalRevenue: dashboardData.totalRevenue,
            todayOrders: dashboardData.todayOrders
          } : {
            totalCustomers: dashboardData.totalCustomers,
            totalOrders: dashboardData.totalOrders,
            totalRevenue: dashboardData.totalRevenue,
            todayOrders: dashboardData.todayOrders
          },
          contact: contactSection ? {
            title: contactSection.title || '',
            subtitle: contactSection.subtitle || '',
            address: contactSection.address || '',
            phone: contactSection.phone || '',
            hours: contactSection.hours || '',
            email: contactSection.email || '',
            feedbackTitle: contactSection.feedback_form_title || '',
            feedbackSubtitle: contactSection.feedback_form_subtitle || ''
          } : {}
        };
      },

      saveData: async function(data) {
        try {
          // Save each section to the database
          const savePromises = [];

          // Save hero section
          if (data.hero) {
            const heroPayload = {
              section_type: 'hero',
              title: data.hero.title,
              subtitle: data.hero.subtitle,
              highlighted_text: data.hero.highlight,
              primary_button_text: data.hero.btn1Text,
              secondary_button_text: data.hero.btn2Text,
              background_color_1: data.hero.bg1,
              background_color_2: data.hero.bg2,
              background_color_3: data.hero.bg3,
              is_active: true
            };
            savePromises.push(this.updateOrCreateSection('hero', heroPayload));
          }

          // Save about section
          if (data.about) {
            const aboutPayload = {
              section_type: 'about',
              title: data.about.title,
              subtitle: data.about.subtitle,
              description: data.about.description,
              feature_1: data.about.feature1,
              feature_2: data.about.feature2,
              feature_3: data.about.feature3,
              feature_4: data.about.feature4,
              about_primary_button_text: data.about.btn1Text,
              about_secondary_button_text: data.about.btn2Text,
              is_active: true
            };
            savePromises.push(this.updateOrCreateSection('about', aboutPayload));
          }

          // Save statistics section
          if (data.stats) {
            const statsPayload = {
              section_type: 'statistics',
              stat1_icon: data.stats.stat1?.icon,
              stat1_value: data.stats.stat1?.value,
              stat1_label: data.stats.stat1?.label,
              stat2_icon: data.stats.stat2?.icon,
              stat2_value: data.stats.stat2?.value,
              stat2_label: data.stats.stat2?.label,
              stat3_icon: data.stats.stat3?.icon,
              stat3_value: data.stats.stat3?.value,
              stat3_label: data.stats.stat3?.label,
              stat4_icon: data.stats.stat4?.icon,
              stat4_value: data.stats.stat4?.value,
              stat4_label: data.stats.stat4?.label,
              is_active: true
            };
            savePromises.push(this.updateOrCreateSection('statistics', statsPayload));
          }

          // Save contact section
          if (data.contact) {
            const contactPayload = {
              section_type: 'contact',
              title: data.contact.title,
              subtitle: data.contact.subtitle,
              address: data.contact.address,
              phone: data.contact.phone,
              hours: data.contact.hours,
              email: data.contact.email,
              feedback_form_title: data.contact.feedbackTitle,
              feedback_form_subtitle: data.contact.feedbackSubtitle,
              is_active: true
            };
            savePromises.push(this.updateOrCreateSection('contact', contactPayload));
          }

          await Promise.all(savePromises);
          return true;
        } catch (error) {
          return false;
        }
      },

      updateOrCreateSection: async function(sectionType, payload) {
        try {
          // First, try to find existing record
          const response = await fetch(`/admin/homepage/get-section/${sectionType}`, {
            method: 'GET',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          });

          let url, method;
          if (response.ok) {
            const existingData = await response.json();
            if (existingData.content) {
              // Update existing record
              url = `/admin/homepage/${existingData.content.id}`;
              method = 'PUT';
            } else {
              // Create new record
              url = '/admin/homepage';
              method = 'POST';
            }
          } else {
            // Create new record
            url = '/admin/homepage';
            method = 'POST';
          }

          const saveResponse = await fetch(url, {
            method: method,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });

          if (!saveResponse.ok) {
            throw new Error(`HTTP error! status: ${saveResponse.status}`);
          }

          const result = await saveResponse.json();
          return result;
        } catch (error) {
          throw error;
        }
      },

      loadBookings: function() {
        // Bookings will be loaded from database API when needed
      },

      saveBookings: function() {
        try {
          // Bookings saved via database API
          return true;
        } catch (error) {
          return false;
        }
      }
    };

    // ===== FORM VALIDATOR =====
    const formValidator = {
      validateField: function(fieldId, validationRules) {
        const field = document.getElementById(fieldId);
        if (!field) return true;

        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required validation
        if (validationRules.required && !value) {
          isValid = false;
          errorMessage = 'This field is required';
        }

        // Pattern validation
        if (isValid && validationRules.pattern && value) {
          const regex = new RegExp(validationRules.pattern);
          if (!regex.test(value)) {
            isValid = false;
            errorMessage = validationRules.patternMessage || 'Invalid format';
          }
        }

        // Min length validation
        if (isValid && validationRules.minLength && value.length < validationRules.minLength) {
          isValid = false;
          errorMessage = `Minimum ${validationRules.minLength} characters required`;
        }

        // Max length validation
        if (isValid && validationRules.maxLength && value.length > validationRules.maxLength) {
          isValid = false;
          errorMessage = `Maximum ${validationRules.maxLength} characters allowed`;
        }

        // Custom validation
        if (isValid && validationRules.custom && !validationRules.custom(value)) {
          isValid = false;
          errorMessage = validationRules.customMessage || 'Invalid value';
        }

        if (!isValid) {
          uiService.showFormError(fieldId, errorMessage);
        } else {
          uiService.clearFormError(fieldId);
        }

        return isValid;
      },

      validateForm: function(formId, fieldRules) {
        let isValid = true;

        for (const fieldId in fieldRules) {
          if (!this.validateField(fieldId, fieldRules[fieldId])) {
            isValid = false;
          }
        }

        return isValid;
      }
    };

    // ===== MAIN APPLICATION =====
    document.addEventListener('DOMContentLoaded', async () => {
      // Load all data from storage into the forms
      await loadPageData();
      dataService.loadBookings();

      // Setup tab functionality
      setupTabs();

      // Load specials into the grid
      loadSpecials();

      // Setup modal and CRUD actions for specials
      setupSpecialsManagement();

      // Setup individual form save handlers
      setupFormHandlers();

      // Setup confirmation modal
      setupConfirmationModal();
    });

    // ===== PAGE INITIALIZATION =====
    async function loadPageData() {
      try {
        let data = dataService.getStoredData();

        // Populate form fields with data
        populateFormFields(data);

        // Update specials data
        SPECIALS_DATA = data.promotions?.items || [];

      } catch (error) {
        uiService.showToast('Failed to load page data. Please refresh the page.', 'error');
      }
    }

    function populateFormFields(data) {
      // Hero Section
      const rawTitle = data.hero?.title || 'Welcome to {The Stag}';
      const match = rawTitle.match(/^(.*)\{(.*)\}(.*)$/);
      if (match) {
        document.getElementById('heroTitle').value = `${match[1]}${match[2]}${match[3]}`;
        document.getElementById('heroHighlight').value = match[2];
      } else {
        document.getElementById('heroTitle').value = rawTitle;
        document.getElementById('heroHighlight').value = data.hero?.highlight || 'The Stag';
      }

      document.getElementById('heroSubtitle').value = data.hero?.subtitle || 'Experience premium dining with our signature steaks, authentic Malaysian flavors, and exceptional service that creates unforgettable culinary memories';
      document.getElementById('heroBtn1Text').value = data.hero?.btn1Text || 'Explore Menu';
      document.getElementById('heroBtn2Text').value = data.hero?.btn2Text || 'Learn More';
      document.getElementById('heroBg1').value = data.hero?.bg1 || '#6366f1';
      document.getElementById('heroBg2').value = data.hero?.bg2 || '#5856eb';
      document.getElementById('heroBg3').value = data.hero?.bg3 || '#ff6b35';

      // About Section
      document.getElementById('aboutTitle').value = data.about?.title || 'About The Stag';
      document.getElementById('aboutSubtitle').value = data.about?.subtitle || "Discover the perfect blend of Western cuisine and Malaysian favorites in the heart of the city.";
      document.getElementById('aboutDescription').value = data.about?.description || "At The Stag, we pride ourselves on delivering exceptional dining experiences through carefully crafted dishes, premium ingredients, and warm hospitality. From our signature steaks to authentic local delicacies, every meal tells a story.";
      document.getElementById('aboutFeature1').value = data.about?.feature1 || 'Premium beef steaks aged to perfection';
      document.getElementById('aboutFeature2').value = data.about?.feature2 || 'Authentic Malaysian dishes with a modern twist';
      document.getElementById('aboutFeature3').value = data.about?.feature3 || 'Fresh pasta made daily with imported Italian ingredients';
      document.getElementById('aboutFeature4').value = data.about?.feature4 || 'Award-winning culinary team with years of expertise';
      document.getElementById('aboutBtn1Text').value = data.about?.btn1Text || 'View Full Menu';
      document.getElementById('aboutBtn2Text').value = data.about?.btn2Text || 'Contact Us';

      // Promotions Section
      document.getElementById('specialsTitle').value = data.promotions?.title || "Featured Promotions";
      document.getElementById('specialsSubtitle').value = data.promotions?.subtitle || "Don't miss out on our limited-time offers and special deals!";

      // Stats Section
      document.getElementById('stat1Icon').value = data.stats?.stat1?.icon || '🍽️';
      document.getElementById('stat1Value').value = data.stats?.stat1?.value || '100+';
      document.getElementById('stat1Label').value = data.stats?.stat1?.label || 'Menu Items';
      document.getElementById('stat2Icon').value = data.stats?.stat2?.icon || '⭐';
      document.getElementById('stat2Value').value = data.stats?.stat2?.value || '4.8';
      document.getElementById('stat2Label').value = data.stats?.stat2?.label || 'Customer Rating';
      document.getElementById('stat3Icon').value = data.stats?.stat3?.icon || '👨‍🍳';
      document.getElementById('stat3Value').value = data.stats?.stat3?.value || '15+';
      document.getElementById('stat3Label').value = data.stats?.stat3?.label || 'Years Experience';
      document.getElementById('stat4Icon').value = data.stats?.stat4?.icon || '🏆';
      document.getElementById('stat4Value').value = data.stats?.stat4?.value || '25k+';
      document.getElementById('stat4Label').value = data.stats?.stat4?.label || 'Happy Customers';

      // Contact Section
      document.getElementById('contactTitle').value = data.contact?.title || 'Visit Us Today';
      document.getElementById('contactSubtitle').value = data.contact?.subtitle || "We're located in the heart of the city, ready to serve you exceptional dining experiences.";
      document.getElementById('contactAddress').value = data.contact?.address || '123 Food Street, City Center';
      document.getElementById('contactPhone').value = data.contact?.phone || '+60 12-345-6789';
      document.getElementById('contactHours').value = data.contact?.hours || 'Daily 11AM - 11PM';
      document.getElementById('contactEmail').value = data.contact?.email || 'hello@thestag.com';
      document.getElementById('feedbackTitle').value = data.contact?.feedbackTitle || 'Share Your Feedback';
      document.getElementById('feedbackSubtitle').value = data.contact?.feedbackSubtitle || 'Help us improve by sharing your dining experience with us.';
    }

    // ===== TAB FUNCTIONALITY =====
    function setupTabs() {
      const tabs = document.querySelectorAll('.tab');

      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          const tabId = tab.getAttribute('data-tab');

          // Update active tab
          tabs.forEach(t => t.classList.remove('active'));
          tab.classList.add('active');

          // Show correct content
          document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
          });
          document.getElementById(`${tabId}-tab`).classList.add('active');
        });
      });
    }

    // ===== SPECIALS MANAGEMENT =====
    function loadSpecials() {
      const container = document.getElementById('specialsContainer');
      const emptyState = document.getElementById('specialsEmptyState');

      if (!container) return;

      container.innerHTML = '';

      if (SPECIALS_DATA.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
      }

      container.style.display = 'grid';
      emptyState.style.display = 'none';

      SPECIALS_DATA.forEach(special => {
        const card = document.createElement('div');
        card.className = 'special-card';
        card.dataset.id = special.id;
        card.setAttribute('role', 'article');

        card.innerHTML = `
          <div class="special-header">
            <div class="special-emoji" aria-hidden="true">${special.img}</div>
            <div class="special-actions">
              <button class="admin-btn btn-secondary" data-action="edit" data-id="${special.id}" aria-label="Edit promotion">Edit</button>
              <button class="admin-btn btn-danger" data-action="delete" data-id="${special.id}" aria-label="Delete promotion">Delete</button>
            </div>
          </div>
          <div class="special-title">${special.name}</div>
          <div class="special-desc">${special.description}</div>
          <div class="special-price">RM ${special.price.toFixed(2)}</div>
        `;

        container.appendChild(card);
      });
    }

    function setupSpecialsManagement() {
      const modal = document.getElementById('specialModal');
      if (!modal) return;

      const specialForm = document.getElementById('specialForm');
      const modalTitle = modal.querySelector('.modal-title');
      const addSpecialBtn = document.getElementById('addSpecialBtn');
      const specialsContainer = document.getElementById('specialsContainer');
      const closeButtons = modal.querySelectorAll('[data-dismiss="modal"]');

      // Modal open/close helpers
      const openModal = () => {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
      };

      const closeModal = () => {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        specialForm.reset();
        document.getElementById('specialEditId').value = '';
        uiService.clearAllFormErrors('specialForm');
      };

      // Open modal for adding a new special
      addSpecialBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Add New Promotion';
        specialForm.reset();
        document.getElementById('specialEditId').value = '';
        uiService.clearAllFormErrors('specialForm');
        openModal();
      });

      // Handle Edit and Delete using event delegation on the container
      specialsContainer.addEventListener('click', (e) => {
        const button = e.target.closest('button[data-action]');
        if (!button) return;

        const id = button.dataset.id;
        const action = button.dataset.action;

        if (action === 'edit') {
          const specialToEdit = SPECIALS_DATA.find(s => s.id === id);
          if (specialToEdit) {
            modalTitle.textContent = 'Edit Promotion';
            document.getElementById('specialEditId').value = specialToEdit.id;
            document.getElementById('specialName').value = specialToEdit.name;
            document.getElementById('specialPrice').value = specialToEdit.price;
            document.getElementById('specialEmoji').value = specialToEdit.img;
            document.getElementById('specialLink').value = specialToEdit.link;
            document.getElementById('specialDescription').value = specialToEdit.description;
            openModal();
          }
        } else if (action === 'delete') {
          showConfirmModal('Are you sure you want to delete this promotion?', () => {
            SPECIALS_DATA = SPECIALS_DATA.filter(s => s.id !== id);
            const data = dataService.getStoredData();
            data.promotions.items = SPECIALS_DATA;
            dataService.saveData(data);
            loadSpecials();
            uiService.showToast('Promotion deleted successfully', 'success');
          });
        }
      });

      // Handle form submission for both Add and Edit
      specialForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // Validate form
        const validationRules = {
          specialName: { required: true },
          specialPrice: { required: true, custom: (value) => !isNaN(value) && parseFloat(value) >= 0, customMessage: 'Please enter a valid price' },
          specialEmoji: { maxLength: 2 },
          specialLink: { required: true },
          specialDescription: { required: true }
        };

        if (!formValidator.validateForm('specialForm', validationRules)) {
          return;
        }

        const id = document.getElementById('specialEditId').value;
        const specialData = {
          name: document.getElementById('specialName').value.trim(),
          price: parseFloat(document.getElementById('specialPrice').value),
          img: document.getElementById('specialEmoji').value.trim() || '🍽️',
          link: document.getElementById('specialLink').value.trim(),
          description: document.getElementById('specialDescription').value.trim(),
        };

        const submitButton = specialForm.querySelector('button[type="submit"]');
        uiService.toggleButtonLoading(submitButton, true);

        if (id) { // Editing existing special
          const index = SPECIALS_DATA.findIndex(s => s.id === id);
          if (index > -1) {
            SPECIALS_DATA[index] = { ...SPECIALS_DATA[index], ...specialData };
            uiService.showToast('Promotion updated successfully', 'success');
          }
        } else { // Adding new special
          specialData.id = 's' + Date.now();
          SPECIALS_DATA.push(specialData);
          uiService.showToast('Promotion added successfully', 'success');
        }

        const data = dataService.getStoredData();
        data.promotions.items = SPECIALS_DATA;
        dataService.saveData(data);
        loadSpecials();
        closeModal();
        uiService.toggleButtonLoading(submitButton, false);
      });

      // Listeners to close the modal
      closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
      });

      // Close modal when clicking outside
      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closeModal();
        }
      });

      // Handle escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
          closeModal();
        }
      });
    }

    // ===== FORM HANDLERS =====
    function setupFormHandlers() {
      // Hero form
      const heroForm = document.getElementById('heroForm');
      if (heroForm) {
        heroForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          // Validate form
          const validationRules = {
            heroTitle: { required: true },
            heroHighlight: { required: true },
            heroSubtitle: { required: true },
            heroBtn1Text: { required: true },
            heroBtn2Text: { required: true },
            heroBg1: { required: true },
            heroBg2: { required: true },
            heroBg3: { required: true }
          };

          if (!formValidator.validateForm('heroForm', validationRules)) {
            return;
          }

          const submitButton = heroForm.querySelector('button[type="submit"]');
          uiService.toggleButtonLoading(submitButton, true);

          const data = dataService.getStoredData();

          // Construct title with highlighted text in curly braces
          const titleText = document.getElementById('heroTitle').value;
          const highlightText = document.getElementById('heroHighlight').value;
          const titleWithHighlight = titleText.replace(highlightText, `{${highlightText}}`);

          data.hero = {
            title: titleWithHighlight,
            highlight: highlightText,
            subtitle: document.getElementById('heroSubtitle').value,
            btn1Text: document.getElementById('heroBtn1Text').value,
            btn2Text: document.getElementById('heroBtn2Text').value,
            bg1: document.getElementById('heroBg1').value,
            bg2: document.getElementById('heroBg2').value,
            bg3: document.getElementById('heroBg3').value,
          };

          const success = await dataService.saveData(data);
          if (success) {
            uiService.showToast('Hero section saved successfully', 'success');
          } else {
            uiService.showToast('Error saving hero section', 'error');
          }

          uiService.toggleButtonLoading(submitButton, false);
        });
      }

      // About form
      const aboutForm = document.getElementById('aboutForm');
      if (aboutForm) {
        aboutForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          // Validate form
          const validationRules = {
            aboutTitle: { required: true },
            aboutSubtitle: { required: true },
            aboutDescription: { required: true },
            aboutFeature1: { required: true },
            aboutFeature2: { required: true },
            aboutFeature3: { required: true },
            aboutFeature4: { required: true },
            aboutBtn1Text: { required: true },
            aboutBtn2Text: { required: true }
          };

          if (!formValidator.validateForm('aboutForm', validationRules)) {
            return;
          }

          const submitButton = aboutForm.querySelector('button[type="submit"]');
          uiService.toggleButtonLoading(submitButton, true);

          const data = dataService.getStoredData();
          data.about = {
            title: document.getElementById('aboutTitle').value,
            subtitle: document.getElementById('aboutSubtitle').value,
            description: document.getElementById('aboutDescription').value,
            feature1: document.getElementById('aboutFeature1').value,
            feature2: document.getElementById('aboutFeature2').value,
            feature3: document.getElementById('aboutFeature3').value,
            feature4: document.getElementById('aboutFeature4').value,
            btn1Text: document.getElementById('aboutBtn1Text').value,
            btn2Text: document.getElementById('aboutBtn2Text').value,
          };

          const success = await dataService.saveData(data);
          if (success) {
            uiService.showToast('About section saved successfully', 'success');
          } else {
            uiService.showToast('Error saving about section', 'error');
          }

          uiService.toggleButtonLoading(submitButton, false);
        });
      }

      // Specials section titles form
      const specialsForm = document.getElementById('specialsForm');
      if (specialsForm) {
        specialsForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          // Validate form
          const validationRules = {
            specialsTitle: { required: true },
            specialsSubtitle: { required: true }
          };

          if (!formValidator.validateForm('specialsForm', validationRules)) {
            return;
          }

          const submitButton = specialsForm.querySelector('button[type="submit"]');
          uiService.toggleButtonLoading(submitButton, true);

          const data = dataService.getStoredData();
          if (!data.promotions) data.promotions = {};
          data.promotions.title = document.getElementById('specialsTitle').value;
          data.promotions.subtitle = document.getElementById('specialsSubtitle').value;

          const success = await dataService.saveData(data);
          if (success) {
            uiService.showToast('Promotions section saved successfully', 'success');
          } else {
            uiService.showToast('Error saving promotions section', 'error');
          }

          uiService.toggleButtonLoading(submitButton, false);
        });
      }

      // Stats form
      const statsForm = document.getElementById('statsForm');
      if (statsForm) {
        statsForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          // Validate form
          const validationRules = {
            stat1Icon: { required: true },
            stat1Value: { required: true },
            stat1Label: { required: true },
            stat2Icon: { required: true },
            stat2Value: { required: true },
            stat2Label: { required: true },
            stat3Icon: { required: true },
            stat3Value: { required: true },
            stat3Label: { required: true },
            stat4Icon: { required: true },
            stat4Value: { required: true },
            stat4Label: { required: true }
          };

          if (!formValidator.validateForm('statsForm', validationRules)) {
            return;
          }

          const submitButton = statsForm.querySelector('button[type="submit"]');
          uiService.toggleButtonLoading(submitButton, true);

          const data = dataService.getStoredData();
          data.stats = {
            stat1: {
              icon: document.getElementById('stat1Icon').value,
              value: document.getElementById('stat1Value').value,
              label: document.getElementById('stat1Label').value
            },
            stat2: {
              icon: document.getElementById('stat2Icon').value,
              value: document.getElementById('stat2Value').value,
              label: document.getElementById('stat2Label').value
            },
            stat3: {
              icon: document.getElementById('stat3Icon').value,
              value: document.getElementById('stat3Value').value,
              label: document.getElementById('stat3Label').value
            },
            stat4: {
              icon: document.getElementById('stat4Icon').value,
              value: document.getElementById('stat4Value').value,
              label: document.getElementById('stat4Label').value
            },
          };

          const success = await dataService.saveData(data);
          if (success) {
            uiService.showToast('Statistics section saved successfully', 'success');
          } else {
            uiService.showToast('Error saving statistics section', 'error');
          }

          uiService.toggleButtonLoading(submitButton, false);
        });
      }

      // Contact form
      const contactForm = document.getElementById('contactForm');
      if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          // Validate form
          const validationRules = {
            contactTitle: { required: true },
            contactSubtitle: { required: true },
            contactAddress: { required: true },
            contactPhone: { required: true, pattern: "[\\+]?[0-9\\s\\-\\()]+" },
            contactHours: { required: true },
            contactEmail: { required: true, pattern: "[^@\\s]+@[^@\\s]+\\.[^@\\s]+" },
            feedbackTitle: { required: true },
            feedbackSubtitle: { required: true }
          };

          if (!formValidator.validateForm('contactForm', validationRules)) {
            return;
          }

          const submitButton = contactForm.querySelector('button[type="submit"]');
          uiService.toggleButtonLoading(submitButton, true);

          const data = dataService.getStoredData();
          data.contact = {
            title: document.getElementById('contactTitle').value,
            subtitle: document.getElementById('contactSubtitle').value,
            address: document.getElementById('contactAddress').value,
            phone: document.getElementById('contactPhone').value,
            hours: document.getElementById('contactHours').value,
            email: document.getElementById('contactEmail').value,
            feedbackTitle: document.getElementById('feedbackTitle').value,
            feedbackSubtitle: document.getElementById('feedbackSubtitle').value,
          };

          const success = await dataService.saveData(data);
          if (success) {
            uiService.showToast('Contact section saved successfully', 'success');
          } else {
            uiService.showToast('Error saving contact section', 'error');
          }

          uiService.toggleButtonLoading(submitButton, false);
        });
      }

      // Setup reset buttons
      setupResetButtons();
    }

    function setupResetButtons() {
      const resetButtons = document.querySelectorAll('button[id$="Btn"][class*="btn-secondary"]');

      resetButtons.forEach(button => {
        // Exclude the main preview button and modal cancel button
        if (button.id === 'previewBtn' || button.closest('.modal-footer')) return;

        button.addEventListener('click', (e) => {
          e.preventDefault();
          const formId = button.id.replace('reset', '').replace('Btn', '').toLowerCase() + 'Form';
          const form = document.getElementById(formId);

          if (form) {
            showConfirmModal('Are you sure you want to reset this form? All unsaved changes will be lost.', () => {
              form.reset();
              uiService.clearAllFormErrors(formId);
              uiService.showToast('Form has been reset', 'success');
            });
          }
        });
      });
    }

    // ===== CONFIRMATION MODAL =====
    let confirmationCallback = null;

    function showConfirmModal(text, onConfirm) {
      document.getElementById('confirm-modal-text').textContent = text;
      confirmationCallback = onConfirm;
      document.getElementById('confirm-modal').style.display = 'flex';
      document.getElementById('confirm-modal').setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';

      // Focus the confirm button
      setTimeout(() => {
        document.getElementById('confirm-modal-confirm-btn').focus();
      }, 100);
    }

    function setupConfirmationModal() {
      const modal = document.getElementById('confirm-modal');
      const closeBtn = document.getElementById('confirm-modal-close-btn');
      const cancelBtn = document.getElementById('confirm-modal-cancel-btn');
      const confirmBtn = document.getElementById('confirm-modal-confirm-btn');

      const closeModal = () => {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        confirmationCallback = null;
      };

      const handleConfirm = () => {
        if (typeof confirmationCallback === 'function') {
          confirmationCallback();
        }
        closeModal();
      };

      closeBtn.addEventListener('click', closeModal);
      cancelBtn.addEventListener('click', closeModal);
      confirmBtn.addEventListener('click', handleConfirm);

      // Close modal when clicking outside
      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closeModal();
        }
      });

      // Handle escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
          closeModal();
        }
      });
    }

    // ===== ACCESSIBILITY ENHANCEMENTS =====
    // Add real-time validation feedback
    document.addEventListener('input', (e) => {
      if (e.target.classList.contains('form-input') ||
          e.target.classList.contains('form-select') ||
          e.target.classList.contains('form-textarea')) {
        const fieldId = e.target.id;
        const errorElement = document.getElementById(`${fieldId}-error`);

        if (errorElement && errorElement.style.display === 'block') {
          // Clear error as user types
          uiService.clearFormError(fieldId);
        }
      }
    });
  </script>
@endsection
