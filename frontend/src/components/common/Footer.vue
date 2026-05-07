<template>
  <footer class="main-footer">
    <div class="footer-container">
      <div class="footer-left">
        <div class="footer-logo">
          <div class="footer-logo-placeholder">PMS</div>
          <span class="footer-text">Procurement Management System</span>
        </div>
        <p class="copyright">© {{ currentYear }} Procurement System. All rights reserved.</p>
      </div>
      
      <div class="footer-center">
        <div class="footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/purchase-requisitions">Purchase Requests</a></li>
            <li><a href="/vendors">Vendors</a></li>
            <li><a href="/reports">Reports</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h4>Resources</h4>
          <ul>
            <li><a href="/manual/bn.pdf" target="_blank">User Manual (BN)</a></li>
            <li><a href="/manual/en.pdf" target="_blank">User Manual (EN)</a></li>
            <li><a href="/help">Help Center</a></li>
            <li><a href="/contact">Contact Support</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-right">
        <div class="footer-contact">
          <h4>Contact Info</h4>
          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <span>support@procurement.gov.bd</span>
          </div>
          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <span>+880 2-1234567</span>
          </div>
          <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Dhaka, Bangladesh</span>
          </div>
        </div>
        
        <div class="footer-social">
          <h4>Follow Us</h4>
          <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const currentYear = ref(new Date().getFullYear())
</script>

<style scoped>
.main-footer {
  background: linear-gradient(135deg, #122B5A 0%, #1e3a8a 100%);
  color: white;
  padding: 40px 0 20px;
  margin-top: auto;
}

.footer-container {
  display: flex;
  justify-content: space-between;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 30px;
}

.footer-left {
  flex: 1;
  min-width: 250px;
}

.footer-logo {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.footer-logo-placeholder {
  height: 40px;
  margin-right: 15px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-weight: bold;
  font-size: 16px;
  width: 40px;
}

.footer-text {
  font-size: 16px;
  font-weight: 600;
}

.copyright {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
  line-height: 1.5;
}

.footer-center {
  display: flex;
  gap: 60px;
  flex: 2;
}

.footer-links h4 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: white;
}

.footer-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 14px;
}

.footer-links a:hover {
  color: white;
}

.footer-right {
  flex: 1;
  min-width: 250px;
}

.footer-contact h4,
.footer-social h4 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: white;
}

.contact-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  font-size: 14px;
}

.contact-item i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
  color: #4CAF50;
}

.social-links {
  display: flex;
  gap: 15px;
}

.social-link {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
}

.social-link:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
}

/* Mobile responsive */
@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    gap: 30px;
  }
  
  .footer-center {
    flex-direction: column;
    gap: 30px;
  }
  
  .footer-left,
  .footer-center,
  .footer-right {
    min-width: auto;
    flex: auto;
  }
}

@media (max-width: 480px) {
  .main-footer {
    padding: 30px 0 15px;
  }
  
  .footer-container {
    padding: 0 15px;
  }
}
</style>
