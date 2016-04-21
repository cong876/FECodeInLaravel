<!doctype html>
<html lang="en">

<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>

</head>


<body>



<script>

    (function(){
        var pusher = new Pusher('cdf04e644aa253187f40', {
            encrypted: true
        });

        Pusher.log = function(message) {
            if (window.console && window.console.log) {
                window.console.log(message);
            }
        };

        var channel = pusher.subscribe('test');
        channel.bind('App\\Events\\UserTalkedThroughWeChat', function(data) {
            alert('fuck');
            console.log(data);
        });
    })();
</script>
</body>
</html>