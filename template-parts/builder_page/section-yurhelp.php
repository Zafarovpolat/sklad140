<section class="section feedback">
  <div class="container">
    <div class="feedback-block">
      <div class="feedback-text">
        <?php 
        $title = get_field('form_title', 'option');
        if ($title): ?>
          <h2 class="feedback__title title-lg dark"><?= $title; ?></h2>
        <?php endif; ?>
        <?php 
        $text = get_field('form_text', 'option');
        if ($text): ?>
          <p class="text-lg dark feedback__descr"><?= $text; ?></p>
        <?php endif; ?>
      </div>
      <form class="feedback-form light-bg" action="#">
        <div class="feedback-items">
          <div class="input-wrapper" data-type="name">
            <input type="text" name="name" class="input input-grey" placeholder="Ваше имя" required>
            <div class="field-error"></div>
          </div>
          <div class="input-wrapper" data-type="email">
            <input type="text" name="email" class="input input-grey" placeholder="Ваша почта" required>
            <div class="field-error"></div>
          </div>
          <div class="input-wrapper" data-type="phone">
            <input type="text" name="phone" class="input input-grey phone_mask" placeholder="+7 (___) ___-__-__" required>
            <div class="field-error"></div>
          </div>
          <button class="button feedback-form__button" type="submit">Получить консультацию</button>
          <label for="checkbox-2" class="checkbox-wrapper feedback-checkbox">
            <input checked id="checkbox-2" type="checkbox" name="agree" value="1" class="checkbox checkbox-primary">
            <div class="checkbox-fake"></div>
            <p class="base">
              Отправляя заявку вы даёте свое согласие на обработку
              <a href="" class="base link underline">персональных данных</a>
            </p>
          </label>
          <div class="form-status" aria-live="polite"></div>
        </div>
      </form>
    </div>
  </div>
</section>