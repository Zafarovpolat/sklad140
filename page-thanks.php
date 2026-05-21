<?php
/**
 * Template Name: Спасибо
 */
get_header();
?>
<section class="section thank">
  <div class="container">
    <div class="thank-block">
      <div class="thank-content">
        <h2 class="thank__title title-lg dark"><span class="main-color">Имя,</span> <br> спасибо за обращение в
          компанию <br>
          «Контроль-Франшиз»</h2>
        <a href="/" class="button thank__button">Вернуться на главную</a>
      </div>

      <img src="/wp-content/themes/franch1/images/thank.png" alt="img">
    </div>
  </div>
</section>
<script>
document.addEventListener("DOMContentLoaded",function(){
  var name = sessionStorage.getItem("user_name");
  if(name){
    var title = document.querySelector(".thank__title .main-color");
    if(title){
      title.textContent = name + ",";
    }
    sessionStorage.removeItem("user_name");
  }
});
</script>
<?php get_footer(); ?>