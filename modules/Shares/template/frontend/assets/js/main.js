'user strict';

// Preloader
$(window).on('load', function () {
    $('.preloader').fadeOut(1000);
});


//Menu Dropdown
$("ul>li>.sub-menu").parent("li").addClass("has-sub-menu");

$('.menu li a').on('click', function () {
  var element = $(this).parent('li');
  if (element.hasClass('open')) {
    element.removeClass('open');
    element.find('li').removeClass('open');
    element.find('ul').slideUp(300, "swing");
  } else {
    element.addClass('open');
    element.children('ul').slideDown(300, "swing");
    element.siblings('li').children('ul').slideUp(300, "swing");
    element.siblings('li').removeClass('open');
    element.siblings('li').find('li').removeClass('open');
    element.siblings('li').find('ul').slideUp(300, "swing");
  }
});

// Responsive Menu
var headerTrigger = $('.header-trigger');
headerTrigger.on('click', function(){
    $('.menu, .header-trigger').toggleClass('active')
    $('.overlay').toggleClass('active')
});

var headerTrigger2 = $('.top-bar-trigger');
headerTrigger2.on('click', function(){
    $('.header-top').toggleClass('active')
    $('.overlay').addClass('active')
    $('.overlay').removeClass('active')
});

// Overlay Event
var over = $('.overlay');
over.on('click', function() {
  $('.overlay').removeClass('overlay-color')
  $('.overlay').removeClass('active')
  $('.menu, .header-trigger').removeClass('active')
  $('.header-top').removeClass('active')
  $('.dashboard__sidebar').removeClass('active')
})


// // Sticky Menu
// window.addEventListener('scroll', function(){
//   var header = document.querySelector('.header-bottom');
//   header.classList.toggle('sticky', window.scrollY > 0);
// });

// Nice Select
$('.nice-select').niceSelect();

// Scroll To Top 
var scrollTop = $(".scrollToTop");
$(window).on('scroll', function () {
  if ($(this).scrollTop() < 500) {
    scrollTop.removeClass("active");
  } else {
    scrollTop.addClass("active");
  }
});

//Click event to scroll to top
$('.scrollToTop').on('click', function () {
  $('html, body').animate({
    scrollTop: 0
  }, 300);
  return false;
});


$('.header-top-trigger').on('click', function() {
  var e = $('.header-top')
  if(e.hasClass('active')) {
    $('.header-top').removeClass('active')
    $('.overlay').removeClass('active')
  }else {
    $('.header-top').addClass('active')
    $('.overlay').addClass('active')
  }
})


$('.testimonial__content__slider').slick({
  fade: false,
  slidesToShow: 1,
  slidesToScroll: 1,
  swipeToSlide: true,
  infinite: true,
  autoplay: true,
  pauseOnHover: true,
  centerMode: false,
  dots: true,
  asNavFor: '.testimonial__img__slider',
  arrows: true,
  nextArrow: '<i class="las la-arrow-right arrow-right"></i>',
  prevArrow: '<i class="las la-arrow-left arrow-left"></i> ',
 
});

$('.testimonial__img__slider').slick({
  fade: true,
  slidesToShow: 1,
  slidesToScroll: 1,
  swipeToSlide: true,
  infinite: true,
  autoplay: true,
  pauseOnHover: true,
  centerMode: false,
  dots: false,
  asNavFor: '.testimonial__content__slider',
  arrows: false,
});


$('.feature__slider').slick({
  fade: false,
  slidesToShow: 4,
  slidesToScroll: 1,
  infinite: true,
  autoplay: true,
  pauseOnHover: true,
  centerMode: false,
  dots: false,
  arrows: true,
  nextArrow: '<i class="las la-arrow-right arrow-right"></i>',
  prevArrow: '<i class="las la-arrow-left arrow-left"></i> ',
  responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 575,
      settings: {
        slidesToShow: 1,
      }
    },

  ]
});
$('.plan__slider').slick({
  fade: false,
  slidesToShow: 4,
  slidesToScroll: 1,
  infinite: true,
  autoplay: true,
  pauseOnHover: true,
  centerMode: false,
  dots: false,
  arrows: true,
  nextArrow: '<i class="las la-arrow-right arrow-right"></i>',
  prevArrow: '<i class="las la-arrow-left arrow-left"></i> ',
  responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 575,
      settings: {
        slidesToShow: 1,
      }
    },

  ]
});

$('.brand__slider').slick({
  fade: false,
  slidesToShow: 4,
  slidesToScroll: 1,
  infinite: true,
  autoplay: true,
  pauseOnHover: true,
  centerMode: false,
  dots: false,
  arrows: false,
  responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 4,
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 575,
      settings: {
        slidesToShow: 2,
      }
    },

  ]
});

$('.testimonial__slider__two').slick({
  fade: false,
  slidesToShow: 3,
  slidesToScroll: 1,
  infinite: true,
  autoplay: true,
  pauseOnHover: true,
  centerMode: false,
  dots: false,
  arrows: false,
  responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 1,
      }
    },
  ]
});


// Odometer Counter
$(".counter__item, .dashboard__card__item").each(function () {
  $(this).isInViewport(function (status) {
    if (status === "entered") {
      for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
        var el = document.querySelectorAll('.odometer')[i];
        el.innerHTML = el.getAttribute("data-odometer-final");
      }
    }
  });
});


//Faq
$('.faq__item-title').on('click', function (e) {
  var element = $(this).parent('.faq__item');
  if (element.hasClass('open')) {
    element.removeClass('open');
    element.find('.faq__item-content').removeClass('open');
    element.find('.faq__item-content').slideUp(300, "swing");
  } else {
    element.addClass('open');
    element.children('.faq__item-content').slideDown(300, "swing");
    element.siblings('.faq__item').children('.faq__item-content').slideUp(300, "swing");
    element.siblings('.faq__item').removeClass('open');
    element.siblings('.faq__item').find('.faq-title').removeClass('open');
    element.siblings('.faq__item').find('.faq__item-content').slideUp(300, "swing");
  }
});


$('.search--btn').on('click', function() {
  $('.search__form__wrapper').addClass('active')
  $('.overlay').addClass('active')
})

$('.btn-close, .overlay').on('click', function() {
  $('.search__form__wrapper').removeClass('active')
  $('.overlay').removeClass('active')
})


$('.user-thumb').on('click', function() {
  $('.dashboard__sidebar').addClass('active')
  $('.overlay').addClass('active')
})

$('.btn-close, .overlay').on('click', function() {
  $('.dashboard__sidebar').removeClass('active')
  $('.overlay').removeClass('active')
})

// Header Right Clone to Bottom
$('.right__area .user__thumb').clone().appendTo('.mobile-nav-right');
$('.right__area .nice-select').clone().appendTo('.mobile-nav-right');


// Privacy Tab Menu
$('.privacy__tab__menu li a').on('click', function() {
  $('.privacy__tab__menu li a').removeClass('active')
  $(this).addClass('active')
})