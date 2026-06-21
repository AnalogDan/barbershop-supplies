<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
$currentPage = 'contact-us';
?>

<style>
    main {
        background-color: #ffffffff;
    }

    .gray-container {
        background-color: #e2e2e2ff;
        width: 90%;
        margin: 20px auto 5rem auto;
        padding: 4rem 3rem 3rem 3rem;
        min-height: auto;
        box-shadow: 10px 10px 12px rgba(0, 0, 0, 0.4);
        border-radius: 10px;
        display: flex;
    }

    /*Containers*/
    .info-container {
        width: 90%;
        margin: 20px auto 0 auto;
        height: 10rem;
        display: flex;
        align-items: center;
    }

    .info-row {
        height: 5rem;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info1 {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 33%;

    }

    .info2 {
        height: 100%;
        width: 33%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .info3 {
        height: 100%;
        width: 33%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /*Black squares*/
    .square {
        height: 5rem;
        width: 5rem;
        background: #2b2b2b;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center
    }

    .location-icon {
        color: white;
        font-size: 2.5rem;
    }

    .text {
        margin-left: 1.2rem;
        font-size: 1rem;
        font-weight: 600;
        color: #656565ff;
        max-width: 11rem;
    }

    .phone-icon {
        color: white;
        font-size: 2.5rem;
    }

    .insta-icon {
        height: 3rem;
        width: auto;
    }

    /*Maps*/
    .map {
        height: 100%;
        width: 70%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .hours {
        height: 100%;
        width: 30%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .title {
        font-size: 1.8rem;
        color: black;
        font-weight: 600;
        margin-bottom: 2rem;
    }

    .days {
        font-size: 1.3rem;
        color: #656565ff;
        font-weight: 600;
    }

    .times {
        font-size: 1.3rem;
        color: #656565ff;
        font-weight: 600;
        margin-bottom: 1.8rem;
    }

    .address-divider {
        width: 90%;
        margin: 40px auto;
        border: none;
        border-top: 2px solid #666666;
    }

    .instagram-link {
        cursor: pointer;
    }
</style>


<!DOCTYPE html>
<html lang="en">
<?php include '../includes/head.php'; ?>
<?php include '../includes/navbar.php'; ?>

<body>
    <main>
        <div class="hero">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-5">
                        <div class="intro-excerpt">
                            <h1>Contact us</h1>
                            <p class="mb-4">Have a question? Our team is ready to help.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address 1 -->
        <div class="info-container">
            <div class="info-row">
                <div class="info1">
                    <div class="square">
                        <i class="fa-solid fa-location-dot location-icon"></i>
                    </div>
                    <div class=text>
                        1911 S Main St, Santa Ana, CA 92707
                    </div>
                </div>
                <div class="info2 instagram-link">
                    <div class="square">
                        <img class="insta-icon" src="images/instagram-gray.png">
                    </div>
                    <div class=text>
                        @new_vision_barbersupplies
                    </div>
                </div>
                <div class="info3">
                    <div class="square">
                        <i class="fa-solid fa-phone phone-icon"></i>
                    </div>
                    <div class=text>
                        562-312-0559
                    </div>
                </div>
            </div>
        </div>
        <div class="gray-container">
            <div class="map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3318.4339585957846!2d-117.87026582445422!3d33.723592973282486!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80dcd9f22ff3c4c3%3A0xd8847a7abbccd22d!2sNew%20Vision%20Barber%20Supplies!5e0!3m2!1ses-419!2smx!4v1782075888498!5m2!1ses-419!2smx"
                    width="800"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="hours">
                <div class="title">Hours</div>
                <div class="days">Monday - Friday</div>
                <div class="times">10 AM - 7 PM</div>

                <div class="days">Saturday</div>
                <div class="times">9 AM - 5 PM</div>

                <div class="times">Sunday closed</div>
            </div>
        </div>

        <!-- Address 2 -->
        <hr class="address-divider">
        <div class="info-container">
            <div class="info-row">
                <div class="info1">
                    <div class="square">
                        <i class="fa-solid fa-location-dot location-icon"></i>
                    </div>
                    <div class=text>
                        5546 South St, Lakewood, CA 90713
                    </div>
                </div>
                <div class="info2 instagram-link">
                    <div class="square">
                        <img class="insta-icon" src="images/instagram-gray.png">
                    </div>
                    <div class=text>
                        @new_vision_barbersupplies
                    </div>
                </div>
                <div class="info3">
                    <div class="square">
                        <i class="fa-solid fa-phone phone-icon"></i>
                    </div>
                    <div class=text>
                        657-247-4903
                    </div>
                </div>
            </div>
        </div>
        <div class="gray-container">
            <div class="map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d414.1456657269942!2d-118.12414652021582!3d33.859629928397254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80dd3349d9a5b47b%3A0xfd8e6879d2aa54a7!2sNew%20Vision%20Barber%20Supplies!5e0!3m2!1ses-419!2smx!4v1782075740215!5m2!1ses-419!2smx"
                    width="800"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="hours">
                <div class="title">Hours</div>
                <div class="days">Monday</div>
                <div class="times">11 AM - 6 PM</div>

                <div class="days">Tuesday - Friday</div>
                <div class="times">10 AM - 6 PM</div>

                <div class="days">Saturday</div>
                <div class="times">10 AM - 4 PM</div>

                <div class="times">Sunday closed</div>
            </div>
        </div>

        <?php
        include '../includes/footer2.php'
        ?>
        <script>
            //instagram link
            document.querySelectorAll('.instagram-link').forEach(element => {
                element.addEventListener('click', () => {
                    window.open(
                        'https://www.instagram.com/new_vision_barbersupplies/',
                        '_blank'
                    );
                });
            });
        </script>
    </main>
    <link rel="stylesheet" href="css/contact-us.mobile.css">
</body>

</html>