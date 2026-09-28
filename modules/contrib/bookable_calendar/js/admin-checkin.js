(function (Drupal) {
  Drupal.behaviors.BookableCalendarAdminCheckin = {
    attach: function (context, settings) {
      new Vue({
        el: '#bookable-calendar-admin-checkin',
        data: {
          bookings: [],
          calendar_id: null
        },
        methods: {
          checkInOut(id, checked_in) {
            $url = `/bookable-calendar/api/${id}/check-out`
            if (checked_in) {
              $url = `/bookable-calendar/api/${id}/check-in`
            }

            const options = {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              }
            }

            fetch($url, options)
              .then(res => res.json())
          },
          getList() {
            const options = {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json'
              }
            }

            fetch(`/bookable-calendar/api/${this.calendar_id}/bookings`, options)
              .then(res => res.json())
              .then(res => this.bookings = res.data);
          }
        },
        mounted() {
          const path = window.location.pathname;
          this.calendar_id = path.replace('/admin/bookable-calendar/', '').replace('/check-in', '');
          this.getList();
          // fetch a new list every 1 minute to sync if multiple people are checking in at the same time
          window.setInterval(() => {
            this.getList()
          }, 60000)
        },
        computed: {
          byDate() {
            if (this.bookings.length) {
              let bookings_by_date = {};
              this.bookings.forEach(booking => {
                if (!bookings_by_date.hasOwnProperty(booking.date)) {
                  bookings_by_date[booking.date] = [];
                }
                bookings_by_date[booking.date].push(booking);
              });
              return bookings_by_date;
            }
          }
        }
      });
    }
  };
})(Drupal);
