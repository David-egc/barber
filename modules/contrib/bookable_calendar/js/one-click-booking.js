(function (Drupal) {
  Drupal.behaviors.BookableCalendarOneClickBooking = {
    attach: function (context, settings) {
      if (context !== document) {
        return;
      }
      const bookButtons = context.querySelectorAll('.availability__link:not(.is-disabled):not(.booked-by-me)');
      bookButtons.forEach(element => {
        element.addEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.booking);
      });
      const myBookButtons = context.querySelectorAll('.availability__link.booked-by-me');
      myBookButtons.forEach(element => {
        element.addEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.cancel);
      });
      const diaabledButtons = context.querySelectorAll('.availability__link.is-disabled');
      diaabledButtons.forEach(element => {
        element.addEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.disabled);
      });
    },
    booking: async function (e) {
      e.preventDefault();
      let button = this;
      let openingId = button.dataset.openingInstance;
      let slotsAvailable = parseInt(button.dataset.slotsAvailable);
      if (slotsAvailable <= 0) {
        return;
      }
      let raw = JSON.stringify({
        'contact_info': {
          'email': drupalSettings.one_click_booking.email,
          'party_size': 1,
          'uid': drupalSettings.one_click_booking.uid
        }
      });
      Drupal.ajax({
        url: Drupal.url('ajax/bookable-calendar/' + openingId + '/book'),
        submit: raw,
        progress: {
          type: 'fullscreen',
          message: Drupal.t('Booking...'),
        },
        error: function (e) {
          // @todo Some other error occured on the server, what needs to be done?
        }
      }).execute().then(function (data, result, response) {
        if (response.status === 200) {
          button.removeEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.booking);
          button.addEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.cancel);
          button.classList.add('booked-by-me');
          button.innerText = drupalSettings.one_click_booking.booked_text;
          slotsAvailable--;
          button.dataset.slotsAvailable = slotsAvailable;
          button.dataset.existingBooking = 1;
          if (slotsAvailable <= 1) {
            button.classList.add('fully-booked');
          }
          if (button.parentElement.querySelector('.availability__seats-available')) {
            button.parentElement.querySelector('.availability__seats-available').outerText = slotsAvailable;
          }
        }
        else if (response.status === 201) {
          // @todo Some validation error occured.
        }
      });
    },
    cancel: async function (e) {
      e.preventDefault();
      let button = this;
      let openingId = button.dataset.openingInstance;
      let slotsAvailable = parseInt(button.dataset.slotsAvailable);
      let raw = JSON.stringify({
        'contact_info': {
          'email': drupalSettings.one_click_booking.email,
          'party_size': 1,
          'uid': drupalSettings.one_click_booking.uid
        }
      });
      Drupal.ajax({
        url: Drupal.url('ajax/bookable-calendar/' + openingId + '/cancel'),
        submit: raw,
        progress: {
          type: 'fullscreen',
          message: Drupal.t('Cancelling...'),
        },
        error: function (e) {
          // @todo Some other error occured on the server, what needs to be done?
        }
      }).execute().then(function (data, result, response) {
        if (response.status === 200) {
          button.removeEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.cancel);
          button.addEventListener('click', Drupal.behaviors.BookableCalendarOneClickBooking.booking);
          button.classList.remove('booked-by-me');
          button.classList.remove('fully-booked');
          button.innerText = drupalSettings.one_click_booking.available_text;
          slotsAvailable++;
          button.dataset.slotsAvailable = slotsAvailable;
          button.dataset.existingBooking = 0;
          if (button.parentElement.querySelector('.availability__seats-available')) {
            button.parentElement.querySelector('.availability__seats-available').outerText = slotsAvailable;
          }
        }
        else if (response.status === 201) {
          // @todo Some validation error occured.
        }
      });
    },
    disabled: function (e) {
      e.preventDefault();
      // @todo Should there be any feedback to the user that the button is disabled.
    }
  };
})(Drupal);
