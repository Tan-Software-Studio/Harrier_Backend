<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Harrier</title>


        <!-- Styles -->
        

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
            body {
            background: #00091B;
            color: #fff;
            }


            @keyframes fadeIn {
            from {top: 20%; opacity: 0;}
            to {top: 100; opacity: 1;}
            
            }

            @-webkit-keyframes fadeIn {
            from {top: 20%; opacity: 0;}
            to {top: 100; opacity: 1;}
            
            }

            .wrapper {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            animation: fadeIn 1000ms ease;
            -webkit-animation: fadeIn 1000ms ease;
            
            }

            h1 {
            font-size: 50px;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 0;
            line-height: 1;
            font-weight: 700;
            }

            .dot {
            color: #4FEBFE;
            }

            p {
            text-align: center;
            margin: 18px;
            font-family: 'Muli', sans-serif;
            font-weight: normal;
            
            }

            .icons {
            text-align: center;
            
            }

            .icons i {
            color: #00091B;
            background: #fff;
            height: 15px;
            width: 15px;
            padding: 13px;
            margin: 0 10px;
            border-radius: 50px;
            border: 2px solid #fff;
            transition: all 200ms ease;
            text-decoration: none;
            position: relative;
            }

            .icons i:hover, .icons i:active {
            color: #fff;
            background: none;
            cursor: pointer !important;
            transform: scale(1.2);
            -webkit-transform: scale(1.2);
            text-decoration: none;
            
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
        

                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block ">
                    <h1>Harrier</h1>
                </div>

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                
                <div class="wrapper">
                    <h1>Coming soon<span class="dot">.</span></h1>
                    <p></p>
                    
                </div>
            </div>
        </div>
    </body>
</html>
