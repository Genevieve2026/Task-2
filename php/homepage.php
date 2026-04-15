<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLH Homepage</title>
    <link rel="stylesheet" href="../css/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <header>
        <nav>
            <ul>
                <li><img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo"></li>
                <li><a href="../php/marketplace.php">Marketplace</a></li>
                <li><a href="../php/redirect.php">Categories</a></li>
                <li><a href="../php/redirect.php">Delivery and <br>Collection</a></li>
                <li><a href="../php/redirect.php">About Us</a></li>
                <li><a href="../php/redirect.php">Contact</a></li>
                <li><a href="../php/index.php">Sign in</a></li>
                <li><button><a href="../php/index.php">Join Us Today</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Welcome to Greenfield Local Hub</h1>
            <h1>Find local farmers and producers near you</h1>
            <form>
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" placeholder="What are you looking for? Seasonal items...? Locations...?" name="search">
                </div>
            </form>

            <p>At GLH, we are committed to improving and expanding sustainability efforts in our community. Our team of dedicated professionals is here to support you every step of the way.</p>
            
        </section>

        <section class="card-container intro">
            <h1>Greenfield Local Hub</h1>
            <p>is chosen time and time again for its commitment to sustainability and community<br> 
                engagement as well as caring for our local farmers and producers .</p>
        </section>

        <section class="card-container">
            <div class="card">
                <img src="../pictures/community.webp" alt="community">
                <h2>Community Engagement</h2>
                <p>We actively engage with our local community through events, workshops, and partnerships with local organizations. We believe in fostering a sense of community and supporting local initiatives that promote sustainability.</p>
            </div>
            
            <div class="card">
                <img src="../pictures/locally grown.jpg" alt="locally grown">
                <h2>The importance of local food systems</h2>
                <p>Local food systems play a crucial role in promoting sustainability, reducing carbon footprints, and supporting local economies. By choosing locally grown produce, consumers can contribute to a more sustainable and resilient food system.</p>
            </div>

            <div class="card">
                <img src="../pictures/produce_range.webp" alt="produce range">
                <h2>Ranges of Produce</h2>
                <p>We offer a wide variety of fresh, locally-sourced produce throughout the year, ensuring that our customers have access to the highest quality fruits and vegetables, meats and seafood.</p>
            </div>

            <div class="card">
                <img src="../pictures/sellers.jpg" alt="sellers">
                <h2>Sellers near and dear to you</h2>
                <p>Our platform connects you with trusted local sellers who are passionate about providing fresh, high-quality produce directly from their farms to your table.</p>
            </div>
        </section>
    </main>

    <!===Testimonials from customers and sellers===!>

    <section class="testimonials">
        <div class="testimonials__inner">
            <div class="testimonials__header">
                <h2>What Our Customers and Sellers Say</h2>
                <p>Real feedback from buyers and sellers who love our local produce marketplace.</p>
            </div>
            <div class="testimonials__grid">
                <article class="testimonial-card">
                    <div class="testimonial-card__meta">
                        <img src="../pictures/icon.png" alt="Jane Doe" class="testimonial-card__image">
                        <div>
                            <p class="testimonial-card__badge">Customer</p>
                            <h3 class="testimonial-card__name">Sarah Michelle</h3>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star "></span>
                        </div>
                    </div>
                    <p class="testimonial-card__text">"Greenfield Local Hub has made it easier to find who my local farmers are and have helped me learn about my community!</p>
                </article>
            </div>

             <div class="testimonials__grid">
                <article class="testimonial-card">
                    <div class="testimonial-card__meta">
                        <img src="../pictures/icon.png" alt="Jane Doe" class="testimonial-card__image">
                        <div>
                            <p class="testimonial-card__badge">Customer</p>
                            <h3 class="testimonial-card__name">Jane Doe</h3>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star "></span>
                        </div>
                    </div>
                    <p class="testimonial-card__text">"GLH has transformed the way I shop for produce. The quality is unmatched, and I love supporting local farmers!"</p>
                </article>
            </div>

             <div class="testimonials__grid">
                <article class="testimonial-card">
                    <div class="testimonial-card__meta">
                        <img src="../pictures/icon.png" alt="Jane Doe" class="testimonial-card__image">
                        <div>
                            <p class="testimonial-card__badge">Local Producer</p>
                            <h3 class="testimonial-card__name">Gregory Thomson</h3>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked "></span>
                        </div>
                    </div>
                    <p class="testimonial-card__text">"GLH has allowed me to see the ease and art of selling my produce to those in my community!"</p>
                </article>
            </div>

             <div class="testimonials__grid">
                <article class="testimonial-card">
                    <div class="testimonial-card__meta">
                        <img src="../pictures/icon.png" alt="Jane Doe" class="testimonial-card__image">
                        <div>
                            <p class="testimonial-card__badge">Customer</p>
                            <h3 class="testimonial-card__name">Sylvia Johnson</h3>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star checked"></span>
                            <span class="fa fa-star "></span>
                        </div>
                    </div>
                    <p class="testimonial-card__text">"GLH has made shopping much easier for myself."</p>
                </article>
            </div>
        </div>
    </section>

    <footer>

            <img src="../pictures/GLH logo.png" alt="GLH Logo" class="logo">
            <h2>Sustainable as one, Sustainable together.</h2>

            <div class="contact-info">
                <h3>Contact Us:</h3>
                <h3>Email:</h3>
                <p>GreenfieldLocalHub@gmail.com</p>
                <h3>Phone:</h3>
                <p>123-456-7890</p>
                <h3>Address:</h3>
                <p> 82 Downes Street, London, SW10 7BZ</p>
            </div>
            <div class="social-media">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <div class="quick-links">
                <h3>Quick Links:</h3>
                <ul>
                    <li><a href="marketplace.php">Marketplace</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="delivery&collection.php">Delivery and Collection</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="../php/index.php">Sign in</a></li>
                    <li><a href="../php/index.php">Join Us Today</a></li>
                    <li><a href="../php/GLHLoyalty.php">GLHLoyalty</a></li>
                </ul>
            </div>
            <div class="newsletter">
                <p>Subscribe to our newsletter:</p>
                <form>
                    <input type="email" placeholder="Enter your email" name="email">
                    <button type="submit">Subscribe</button>
                </form>
            </div>
            <div class="banks">
                <p>We accept:</p>
                <img src="../pictures/VISA.png" alt="Visa">
                <img src="../pictures/mastercard.png" alt="MasterCard">
                <img src="../pictures/AMEX.png" alt="American Express">
            </div>
            <div class="legal">
                <p><a href="#">Copyright Notices</a> | <a href="#">Terms of Service</a></p>
                <p><a href="#">Terms and Conditions</a> | <a href="#">Food Safety and Hygiene Regulations</a></p>
                <p><a href="#">Privacy Policy</a> | <a href="#">Return Policy</a></p>

        <p>&copy; 2026 Greenfield Local Hub. All rights reserved.</p>
    </footer>
    
</body>
</html>