<?php
/*
 Template Name: Trang chủ
 */
?>
<?php get_header(); ?>
<main id="main">
  <?php
    // Section 1
    get_template_part('template-parts/home/section', 'feature');

    // Section 2
    echo get_template_part('template-parts/home/section', 'about');

    // Section 3
    get_template_part('template-parts/home/section', 'products');

    // Section 4
    get_template_part('template-parts/home/section', 'banner');

    // Section 5
    get_template_part('template-parts/home/section', 'testimonials');

    // Section 6
    get_template_part('template-parts/home/section', 'post_slider');

    // Section 7
    get_template_part('template-parts/home/section', 'latest_posts');

    // Section 8
    get_template_part('template-parts/home/section', 'partners');

    // Section 9
    get_template_part('template-parts/home/section', 'video'); 
  ?>

   
    <div class="slider mb-5">
        <div class="container">
            <h3 class="title text-primary text-uppercase">bằng chứng khoa học</h3>
            <div class="row mb-5">
                <div class="col-md-12 col-xl-3">
                    <img src="https://placehold.co/203x284" alt="Placeholder Image" class="image mb-5">
                    <div class="description text-center">Giấy tờ 1</div>
                </div>
                <div class="col-md-12 col-xl-3">
                    <img src="https://placehold.co/203x284" alt="Placeholder Image" class="image mb-5">
                    <div class="description text-center">Giấy tờ 1</div>
                </div>
                <div class="col-md-12 col-xl-3">
                    <img src="https://placehold.co/203x284" alt="Placeholder Image" class="image mb-5">
                    <div class="description text-center">Giấy tờ 1</div>
                </div>
                <div class="col-md-12 col-xl-3">
                    <img src="https://placehold.co/203x284" alt="Placeholder Image" class="image mb-5">
                    <div class="description text-center">Giấy tờ 1</div>
                </div>
            </div>
            <div class="w-100 text-center">
                <div class="btn-home">
                    <a href="#">Xem chi tiết</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container mb-5">
        <h3 class="title text-primary text-uppercase">Điểm tin mới nhất</h3>
        <div class="row justify-content-between">
            <div class="col-md-12 col-xl-6 mb-md-5">
                <a href="#" class="d-block">
                    <img src="https://placehold.co/439x290" alt="Placeholder Image" class="image rounded-4">
                </a>
                <h3 class="title multi-line-1 text-start mx-0 w-100">
                    <a href="#" class="d-block">Title Title Title Title Title Title Title</a>
                </h3>
                <div class="description multi-line-3 mb-0">
                    Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả,
                </div>
            </div>
            <div class="col-md-12 col-xl-6">
                <div class="description mb-0 text-center border-bottom border-1">
                    CHĂM SÓC VẾT THƯƠNG VÀ CHỮA LÀNH SẸO
                </div>
                <div class="home-blogs">
                    <div class="row mb-2">
                        <div class="col-md-12 col-xl-4">
                            <a href="#" class="d-block">
                                <img src="https://placehold.co/156x105" alt="Placeholder Image" class="image rounded-4">
                            </a>
                        </div>
                        <div class="col-md-12 col-xl-8">
                            <div class="description multi-line-3 mt-4">
                                Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả,
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 col-xl-4">
                            <a href="#" class="d-block">
                                <img src="https://placehold.co/156x105" alt="Placeholder Image" class="image rounded-4">
                            </a>
                        </div>
                        <div class="col-md-12 col-xl-8">
                            <div class="description multi-line-3 mt-4">
                                Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả,
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 col-xl-4">
                            <a href="#" class="d-block">
                                <img src="https://placehold.co/156x105" alt="Placeholder Image" class="image rounded-4">
                            </a>
                        </div>
                        <div class="col-md-12 col-xl-8">
                            <div class="description multi-line-3 mt-4">
                                Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả,
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 col-xl-4">
                            <a href="#" class="d-block">
                                <img src="https://placehold.co/156x105" alt="Placeholder Image" class="image rounded-4">
                            </a>
                        </div>
                        <div class="col-md-12 col-xl-8">
                            <div class="description multi-line-3 mt-4">
                                Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả,
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 col-xl-4">
                            <a href="#" class="d-block">
                                <img src="https://placehold.co/156x105" alt="Placeholder Image" class="image rounded-4">
                            </a>
                        </div>
                        <div class="col-md-12 col-xl-8">
                            <div class="description multi-line-3 mt-4">
                                Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả Mô tả, Mô tả, Mô tả, Mô tả, Mô tả, Mô tả,
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 text-center">
            <div class="btn-home">
                <a href="#">Xem thêm</a>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
