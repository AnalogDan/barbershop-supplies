<style>
    .custom-footer {
        background-color: #2b2b2b;
        color: white;
        padding: 80px 0 0 0;
        margin: 80px 0 0 0;
        position: relative;
        overflow: visible; 
    }

    .custom-footer-container {
        position: relative;
        max-width: 1200px;
        margin: 0 70px;
        padding: 0 15px;
        overflow: visible;
    }

    .custom-footer-logo {
        font-family: 'OldLondon', serif;
        font-size: 40px;
        font-weight: normal;
        text-decoration: none;
        color: #ffffff;
        user-select: none;
        pointer-events: none;
    }

    .custom-footer-links {
        display: flex;
        justify-content: flex-start;  
        align-items: flex-start;
        gap: 10px;   
        margin-top: 30px;
        flex-wrap: wrap;
        font-weight: 500;
        color: #ffffff;
        font-size: 16px;
    }

    .custom-footer-links a {
        display: inline-block;   
        color: inherit;
        opacity: 0.5;
        text-decoration: none;
        transition: opacity 0.3s ease;
        cursor: pointer;  
    }

    .custom-footer-links a:hover {
        opacity: 1;           
    }

    .custom-footer-col {
        flex: 1;
        min-width: 200px;
        margin-bottom: 20px;
    }

    .custom-footer-col p {
        display: inline-block; 
        margin: 0;
    }
    

    .custom-footer-links p {
        margin: 0;
    }

    .custom-footer-links strong {
        display: block;
        margin-bottom: 5px;
    }

    .custom-footer-copyright {
        font-family: inherit;
        font-weight: 500;
        color: #ffffff;
        opacity: .5;
        font-size: 16px;
        position: relative;  
        top: 80px; 
    }

    .custom-footer-shaver {
        position: absolute;
        top: -330px;
        right: -150px;  
        z-index: 1;
    }

    .custom-footer-shaver img {
        width: 120px;      
        max-width: 120px;  
        height: auto;     
        display: block;   
    }

    .custom-footer-ornament {
        width: 100%;
        height: 100px;
        background: url('/barbershopSupplies/public/images/footer-fade.png') no-repeat center bottom;
        background-size: cover;
    }

    .footer-col-left {
        display: flex;
        align-items: center;
        margin-right: 50px; 
        display: inline-block; 
    }
    .footer-col-text {
        display: inline-block;
    }
    .footer-instagram-icon {
        height: 3em;  
        width: auto;
        transform: translateY(-15px);  
    }

    .footer-col-right {
        display: inline-block; 
    }

    .footer-link-left {
        margin-right: 50px; /
    }

    .footer-link-right {
    }
</style>

<footer class="custom-footer">
    <div class="custom-footer-container">

        <div class="custom-footer-logo-wrap">
            <span class="custom-footer-logo">New Vision<br>Barber Supplies</span>
        </div>

        <div class="custom-footer-links">
            <p>
                <span class="footer-col-left">
                <a href="https://www.instagram.com/new_vision_barbersupplies" target="_blank">

                    <img src="/barbershopSupplies/public/images/instagram-gray.png" class="footer-instagram-icon" alt="Instagram">
                    <span class="footer-col-text">
                            Follow us on Instagram<br>@new_vision_barbersupplies
                        
                    </span>
                    </a>
                </span>
                <span class="footer-col-right">
                    <p>
                        <a href="aboutUs.php" class="footer-link-left">About us</a>
                        <a href="returns.php" class="footer-link-right">Returns and exchanges</a><br>
                        <a href="contactUs.php">Contact us</a>
                    </p>
                </span>
            </p>
        </div>
       

        <div class="custom-footer-copyright">
            &copy; New Vision Barber Supplies
        </div>

        <div class="custom-footer-shaver">
            <img src="../public/images/footer-clipper.png" alt="Shaver">
        </div>
    </div>

    <div class="custom-footer-ornament"></div>
</footer>