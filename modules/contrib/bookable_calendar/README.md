# Bookable Calendar

This aim to be a very easy to use Bookable Calendar module. Whether you're
giving lessons and want your students to be able to book a lesson or a business
trying to stagger traffic into your building, this module aims to get you
up and running as fast as possible.

## Steps to create your first Calendar and take a Booking

If reading isn't your thing, we also have a 12 minute
[video](https://www.youtube.com/watch?v=UFnQnwfg-44) that runs through most of
the basics of Bookable Calendar.

### As an Admin

1. Install module as you would any module
2. Create your first Calendar (Under Content section of menu)
3. Create an Opening for that Calendar

### As an user (currently though admin interface UI coming soon)

1. Create a new Booking Contact filling out what Instance you want and party
size.
2. On save it will tell you whether or not that will work based on max
slots available and other things.

## Entities and what they do

### Bookable Calendar

This is the main Calendar that will be user facing. The thought would be
if you're an individual giving lessons it could be called "Piano Lessons" or
if you're a company selling spots in line for Santa it would say
"Santa at the Mall" or whatever. It's fieldable so you can add any
extra fields needed for your situation by default it has the following fields:

- **Title:** The name of your Calendar
- **Description:** This is a long text field that can be filled out to show any
sort of body text you want to show to users about this calendar.
- **Success Message:** Message displayed after a sucessful registration.
- **Booking Future Time:** This will limit user's ability to book openings
more than X days/weeks/months in the future.
- **Booking Lead Time:** This is the minimum amount time in the future a
Booking can take place. This can be used to limit users booking same
day/week/month booking.
- **Max Party Size:** Limit the max amount of bookings an individual contact
can make to a single Opening.
- **Max Opening Bookings Per User:** Limit the amount of future bookings a
single user can do, will limit off of email address.
- **Slots Per Opening:** The amount of Bookings that can claim a single
opening. For example if your event has hour long openings you let people
register for and you can handle 10 people per hour set this field to '10.'
- **Treat Slots as Parties:** This will change the math of "Max Slots" to
"Max Parties". This will allow you to only have 3 different groups claim
a slot but each group can have as big of a party as your Max Party Size limit.
- **Allow in Progress Bookings:** Allows for bookings to happen as long as
opening has not ended.
- **Enable "One-Click" Booking:** If user is logged in, clicking the "Book"
button will book the opening with the logged in users information but will
only book a single slot.
- **User Notification Email:** If enabled users will get a notification
email when they book an opening.
- **Override Default User Email:** Text in the email is editable on the
main settings page, if you want different text per calendar you will
need to select this.
- **User Email Subject:** The subject of the email sent to the user
if Override Default User Email is set.
- **User Email Body:** The body of the email sent to the user if
Override Default User Email is set.
- **Admin Notification Emails:** If enabled admins will get a
notification email on each booking.
- **Notification Email Recipient Role:** Send email notifications on
bookings to the following roles.
- **Notification Email Recipients:** The email address, or addresses
one per line to receive the emails on bookings.
- **Active:** Whether or not you are currently allowing more bookings, this is
so you can still show the calendar but freeze Bookings.
- **Status:** Standard Drupal status, will hide this calendar from non admins.
- **Calendar Openings:** This is not user created but is an entity reference
to each opening for this Calendar.

### Bookable Calendar Opening

The individual openings for your Bookable Calendar. You can create as many of
these as you'd like for your calendar. This is where you define what dates and
times are available to book. Due to it being a multiple date value with repeat
it's possible you could create only one of these per Bookable Calendar but the
option is there to create as many as needed. The advantage af splitting them
out is then it's easier to turn of a certain Opening whether that be a single
day or opening as there is the status field to quickly turn it on or off vs
editing your repeating date field to remove a day.

- **Title:** Only shown to admins to quickly let you know what opening you're
looking at.
- **Status:** Easy turn it on/off for booking, you can turn the whole calendar
off in the Bookable Calendar or here to turn off a single Opening.
- **Bookable Calendar:** Reference to the calendar this opening is for.
- **Booking Instance:** Not user created but links openings to instances.
- **Date:** The date field that is repeatable for when this Opening occurs.
- **Slots:** The default number of available slots for instances in this
opening. If empty (NB not zero), will use calendar's defaults.

### Bookable Calendar Opening Instance

This is an entity that you don't create directly, but per time slot that is
open based on the date field on Bookable Calendar Opening an instance is
created. Then users will register for indivdual instances. The indivdual
instances are also what shows up on the listing of all bookings you book.

- **Booking:** Points to all the Bookings that have Booked this Instance.
- **Booking Opening:** Points to the parent Opening.
- **Date:** The individual date that this Instance points to.

### Booking Contact

This is the entity that a front end user will be creating when they register.

- **Email:** The email for this booking, this is used emails to confirm the
booking and a link to edit the booking.
- **Party Size:** The amount of people this booking is for, this lets us
know how many slots are being taken by this booking.
- **Booking Instance:** The instance this booking points to.
- **Booking:** Not user created, the bookings this contact is linked to.
- **Notifications:** List of Notifications this contact has received.

### Booking

This is another entity no one creates directly, but when a new Contact
is created a new Booking is created per person in the party size.
This is how we know how many people have booked each instance easily.

- **Created:** Time Booking was created.
- **Booking Instance:** The Booking Instance this Booking is for.
- **Booking Calendar:** The parent calendar this Booking is for. (this seems
redundant but Instances might get cleaned up over time)
- **Contact:** The conctact that owns this Booking.
- **Booking Date:** The date this Booking is for. This is because I see a
situation where you need to clean up old Instances as you will most likely
have thousands over the course of a year, but you might still want
historic records of Bookings and what timeslot they booked.

## Notifications

Bookable Calendar provides the feature that allows users to send reminder
emails using [ECA](https://www.drupal.org/project/eca). We've decided to
use an external module to manage notifications instead of hard-coding them
into our module. The reason for this is that we don't want to make too many
assumptions about how and when notifications should be sent. By using a module
that focuses on workflows, we provide much greater flexibility to our users to
receive notifications in the way that they need them. With this approach, you
can easily customize the default workflow or create new ones to suit your
specific needs.

Previously, only transactional emails were available when a booking was created.
With the version 2.2.5, there is now a default notification email that can be
enabled that will send a reminder email one day prior to the booking.

If you're upgrading from version 2.2.x to 2.2.5+, the database will
automatically install the necessary View and add the notification field to
Booking Contact. The ECA model will also be installed if your website meets the
module requirements of having eca_base, eca_content, eca_user, and eca_views
installed.

If you've already upgraded to version 2.2.5 or higher and are missing the ECA
workflows provided by Bookable Calendar, you can install them using a drush
command:

`drush cim --partial --source=modules/contrib/bookable_calendar/config/optional`

If you're also missing the View named "Booking Notifications," you can install
it using a similar drush command. However, if you've customized any of the
default configuration provided by the module and don't want to lose that, it's
recommended that you import the config views.view.booking_notifications.yml
file through the GUI instead. The drush command it import that is:

`drush cim --partial --source=modules/contrib/bookable_calendar/config/install`

### Modifying Notifications

When you first install the module, all time-based notifications are turned off
by default. To turn them on, go to the ECA Workflow page
(/admin/config/workflow/eca) and enable "Booking Notifications: Day Before".
However, to use this feature, your site must have a properly running cron. The
default notification email will be sent 24 hours before the booked opening to
the email listed, and will be flagged as "sent" with the value of "day_before".

The beauty of ECA is that you can easily modify the default workflow or create
new ones. For a more comprehensive understanding of ECA and its capabilities,
check out their [guide](https://ecaguide.org) or watch the [video about using
ECA with Bookable Calendar](https://www.youtube.com/watch?v=YiCRBlz1N_Q).

It's important to note that there are two parts to this workflow: the "Booking
Notifications" view and the ECA Workflow. By default, the view pulls all
Booking Contacts that are between now and +1 day from now. The ECA Workflow
then loops over the results in the view and sends the email as long as the
notification field does not have the value of "day_before".

### Example of how to make a new Notification

If you want to make a new notification that would email people one week
before their booking here are the steps you would need to take.

#### Update the Booking Notifications View
1. Create a new page, copying the existing one on the Booking Notifications
display
2. Change the Filters on the start date to filter to 1 week ahead.
    1. Change `>= now` to `>= +6 days`
    2. Change `<= +1 day`l to `<= +7 days`
3. Change or take note of or change the machine name

#### Create the New ECA Workflow
1. From the operations dropdown, clone the existing "Booking Notifications:
Day Before" Model
2. Go to edit page of new ECA Model
3. From right sidebar "general" change the name to be "Booking Notifications:
 Week Before"
4. Click the arrow from "Notification" to "Mail" to open the edit screen.
5. Change "expected field value" from "day_before" to be "week_before"
6. Click the "Mark Notification As Sent" Task
7. Change "Field Value" to "week_before"
8. Click the "List Notifications" Task
9. Change the "arguments" to "week_before"
10. Click save on model.

## API

There are some API endpoints exposed so you can create new Bookings
however you'd like.

### Book an Instance

You can book an instance by hitting `/bookable-calendar/{instance_id}/book` then
pass in the body of `contact_info` with each machine name you want to save
with it's value. You can also follow the same method but pass in form-data, but
that doesn't need to be under a key of contact_info.

```javascript
var myHeaders = new Headers();
myHeaders.append("Content-Type", "application/json");

var raw = JSON.stringify({
    "contact_info": {
        "email":"foo@bar.com",
        "party_size":2,
        "uid":1 // not a required field
    }
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("/bookable-calendar/{$instance_id}/book", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
```


### Book Multiple Instances at Once

```javascript
var myHeaders = new Headers();
myHeaders.append("Content-Type", "application/json");

var raw = JSON.stringify({
    "contact_info": {
        "email":"foo@bar.com",
        "party_size":2,
        "uid":1 // not a required field
    },
    "opening_instances: [
      1,
      2,
      3
    ]
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("/bookable-calendar/api/book", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
```


## One-Click Booking

If you enable "One-click" booking, the `one-click-booking.js` which will
only work on the default Calendar page Book buttons. If you are trying
to get this JS to work not on the default page it should work as long
as you're outputting the opening instance display as all the code
needed is on the preprocess for opening instances.

## Compatibility Issues

This module is not compatible with the [BAT module](https://www.drupal.org/project/bat), you cannot have both installed at the same time due using entities of the same name "Booking"

## Todo

- Cleanup no longer matching instances maybe on cron.
- Allow for cleanup on Cron based on settings of old Instances,
Bookings and Contacts if you don't care about things older than X months.

## Quickly Delete Everything

```php
$calendar_storage = \Drupal::entityTypeManager()->getStorage('bookable_calendar');
$calendars = $calendar_storage->loadMultiple();
foreach ($calendars as $calendar) {
  $calendar->delete();
}
$opening_storage = \Drupal::entityTypeManager()->getStorage('bookable_calendar_opening');
$openings = $opening_storage->loadMultiple();
foreach ($openings as $opening) {
  $opening->delete();
}
$instanceStorage = \Drupal::entityTypeManager()->getStorage('bookable_calendar_opening_inst');
$instances = $instanceStorage->loadMultiple();
foreach ($instances as $instance) {
  $instance->delete();
}
$booking_contactStorage = \Drupal::entityTypeManager()->getStorage('booking_contact');
$booking_contacts = $booking_contactStorage->loadMultiple();
foreach ($booking_contacts as $contact) {
  $contact->delete();
}
$booking_storage = \Drupal::entityTypeManager()->getStorage('booking');
$bookings = $booking_storage->loadMultiple();
foreach ($bookings as $booking) {
  $booking->delete();
}
```
