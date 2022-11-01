var header_links = document.querySelectorAll('.nav-link');
var i_sections = document.querySelectorAll('.container-text');

window.addEventListener('scroll', e => {
    document.documentElement.style.setProperty('--scrollTop', `${this.scrollY}px`) // Update method

    // i_sections.forEach(sec => {
    //   let top = window.scrollY;
    //   let offset = sec.offsetTop-210;
    //   let height = sec.offsetHeight;
    //   let id = sec.getAttribute('id');
    //   if(top >= offset && top < offset + height) {
    //     header_links.forEach(links => {
    //       links.classList.remove('hl-active');
    //       document.querySelector('.header-links a[href*=' + id + ']').classList.add('hl-active');
          
    //     })
    //   }
    // })

})
window.onload = function() {
    var divLoader = document.getElementById('main-loader');
    divLoader.classList.add('hidden-loader');
    setInterval(function() {
        divLoader.remove();
    },1300)   
}
$(document).ready(function(){
      $('.container-cards').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        speed: 300,
        autoplay: true,
        autoplaySpeed: 10000,
        responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 10000
      }
    },
    {
      breakpoint: 800,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 10000
      }
    },
    {
      breakpoint: 520,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
      });
});

$(document).ready(function(){
      $('.container-review-cards').slick({
        slidesToShow: 3,
        centerMode: true,
        autoplay: true,
        autoplaySpeed: 5000,
        focusOnSelect: true,
        responsive: [
        {
            breakpoint: 1024,
            settings: {
              arrows: false,

              slidesToShow: 2
            }
          },
          {
            breakpoint: 768,
            settings: {
              arrows: false,

              slidesToShow: 1
            }
          }
        ]
      });


    


});