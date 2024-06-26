</div>

<!-- Jquery JS-->
<script src="/vendor/jquery-3.2.1.min.js"></script>
<!-- Bootstrap JS-->
<script src="/vendor/bootstrap-4.1/popper.min.js"></script>
<script src="/vendor/bootstrap-4.1/bootstrap.min.js"></script>
<!-- Vendor JS       -->
<script src="/vendor/slick/slick.min.js">
</script>
<script src="/vendor/wow/wow.min.js"></script>
<script src="/vendor/animsition/animsition.min.js"></script>
<script src="/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
<script src="/vendor/counter-up/jquery.waypoints.min.js"></script>
<script src="/vendor/counter-up/jquery.counterup.min.js"></script>
<script src="/vendor/circle-progress/circle-progress.min.js"></script>
<script src="/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/vendor/chartjs/Chart.bundle.min.js"></script>
<script src="/vendor/select2/select2.min.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="//cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<!-- full calendar requires moment along jquery which is included above -->
<script src="/vendor/fullcalendar-3.10.0/lib/moment.min.js"></script>
<script src="/vendor/fullcalendar-3.10.0/fullcalendar.js"></script>

<!-- Main JS-->
<script src="/js/main.js?v1.0.11"></script>

<link  href="/css/multiselect.css" rel="stylesheet" />
<script src="/js/multiselect.min.js"></script>
<!-- forgot password script -->

<script type="text/javascript" src="/js/reset_password.js?v={{ config('app.version') }}"></script>

<script src="https://accounts.google.com/gsi/client" async defer></script>


<script type="text/javascript" src="/js/auth.js?v={{ config('app.version') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <link rel="stylesheet" href="sweetalert2.min.css"> -->
<script type="text/javascript">

// $.getScript('https://connect.facebook.net/en_US/sdk.js', function(){

//     FB.init({
//       appId: '1304509377114263',
//       cookie     : true,
//       xfbml      : true,
//       version: 'v16.0'
//     });

// });

$(function() {
  // for now, there is something adding a click handler to 'a'
  var tues = moment().day(2).hour(19);

  // build trival night events for example data
  var events = [
    {
      title: "Special Conference",
      start: moment().format('YYYY-MM-DD'),
      url: '#'
    },
    {
      title: "Doctor Appt",
      start: moment().hour(9).add(2, 'days').toISOString(),
      url: '#'
    }

  ];

  var trivia_nights = []

  for(var i = 1; i <= 4; i++) {
    var n = tues.clone().add(i, 'weeks');
    console.log("isoString: " + n.toISOString());
    trivia_nights.push({
      title: 'Trival Night @ Pub XYZ',
      start: n.toISOString(),
      allDay: false,
      url: '#'
    });
  }

  // setup a few events
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay,listWeek'
    },
    events: events.concat(trivia_nights)
  });



});

</script>

