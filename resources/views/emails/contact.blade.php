<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{project('app_name')}}</title>
    </head>
    <body class="antialiased">
        <h2>Dear, Harrier Canidates</h2> 
        <br>
            
        <strong>User details: </strong><br>
        <strong>Name: </strong>{{ $data->name }} <br>
        <strong>Email: </strong>{{ $data->email }} <br>
        <strong>Phone: </strong>{{ $data->phone }} <br>
        <strong>Subject: </strong>{{ $data->subject }} <br>
        <strong>Message: </strong>{{ $data->message }} <br><br>
        
        Thank you
    </body>
</html>
