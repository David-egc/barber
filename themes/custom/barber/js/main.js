// AOS.init();
const pushbar = new Pushbar({
    blur: true,
    overlay: true
});

const observador = new IntersectionObserver((entries)=>{
    entries[0].intersectionRatio <=0 ?  hidennMenuSecundario():showMenuSecundario()
});
observador.observe(document.querySelector(".region-header"));
// console.log(currentQuestion);
// console.log(totalQuestion);

const hidennMenuSecundario=()=>{
    document.querySelector(".region-menu-secundario").classList.add('d-md-block');
}
const showMenuSecundario=()=>{ 
    document.querySelector(".region-menu-secundario").classList.remove('d-md-block')};


document.querySelectorAll ('.field--name-field-dato').forEach( element => {
   const target = parseInt( element.innerText);
   
   let count = 0;
   const duration = 5000;
   const increment = target/(duration/10);
   const timer = setInterval(() => {
    count += increment ;
    element.innerText= Math.ceil( count) ;
    if ( count >= target) {
      element.innerText = (target+ "+") ;
      clearInterval(timer);
    }
   }, 10)
});


jQuery(document).ready(function () {

    // const currentQuestion = parseInt(document.getElementById('quiz-question-number').textContent, 10);
    // const totalQuestion = parseInt(document.getElementById('quiz-num-questions').textContent, 10);


    // const progress = (currentQuestion / totalQuestion) * 100;
    // document.querySelector('.progress-fill').setAttribute('style', `width:${progress}%`);

    // console.log(progress);
    if (jQuery('form').hasClass('answering-form')) {
        const currentQuestion = parseInt(document.getElementById('quiz-question-number').textContent, 10);
        const totalQuestion = parseInt(document.getElementById('quiz-num-questions').textContent, 10);


        const progress = (currentQuestion / totalQuestion) * 100;
        document.querySelector('.progress-fill').setAttribute('style', `width:${progress}%`);


    }







    jQuery('.view-testimonios').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        slide: '.slide',
        autoplaySpeed: 5000,
    });
    jQuery('.view-testimonios-sm').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        slide: '.slide',
        autoplaySpeed: 2000,
    });



  jQuery('.view-marcas').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        slide: '.slide',
        autoplaySpeed: 5000,
    });
    jQuery('.view-marcas-sm').slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        autoplay: true,
        slide: '.slide',
        autoplaySpeed: 2000,
    });


// Variable para controlar si ya mostramos la alerta
let alertShown = false;
let lastScrollTop = 0;






});



