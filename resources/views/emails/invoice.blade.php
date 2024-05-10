<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS Invoice</title>
</head>
<body>

    <h1>Hello, {{ $data['client_name'] }}</h1>
    <h4 style="color: green;">Your Payment recived successfully.</h4>

    <p>This is an example email sent from <span style="color: blue;"> PMS Invoice </span></p>
    <p>Payment at : {{ $data['payment_date'] }} {{ $data['payment_time'] }}</p>
    <p>Amount : <b>{{ $data['amount'] }}</b></p>

    @if($data['website_name'])
        <p>Website Name : {{ $data['website_name'] }}</p>
    @endif

</body>
</html>