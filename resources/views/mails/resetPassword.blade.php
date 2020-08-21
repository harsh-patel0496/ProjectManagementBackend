<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .root{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .header{
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px;
            
        }
        .innerOne{
            padding: 12px;
            background-color: #63809c;
            color: white !important;
            text-align: center;
        }
        .innerTwo{
            padding: 40px 20px 40px 20px;
            background-color: white;
            
            
        }
        .warpper{
            padding: 0px;
            
        }
        .headerTwo{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .infoText{
            margin-top: 12px;
        }
        .footer{
            margin-top: 25px;
        }
        .btn{
            padding: 0.5rem 1rem;
            font-size: 1.25rem;
            line-height: 1.5;
            border-radius: 0.3rem;
            
            color: white !important;
        }
        .btn-info{
            background-color: #17a2b8;
            border-color: #17a2b8;
            text-decoration:none; 
        }
    </style>
</head>
<body>
    <div class="root row">
        <div class="col-md-12" >
            <div class="header row">
                <div class="warpper col-md-6">
                    <div class="innerOne">
                        <h2>
                            Project Management
                        </h2>
                        <p>Manage your project with agility.</p>
                    </div>
                    <div class="innerTwo">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>
                                    Hi, {{$user['name']}}
                                </h4>
                                <p>
                                    Someone requested that the password for your agile account be reset.
                                </p>
                                <a href="http://localhost:3000/changePassword/{{$user['encryptedEmail']}}" class="btn btn-info btn-lg ">Reset Password</a>
                                <p class="infoText">
                                    If you didn't request this, you can ignore this email or let us know. Your password win't change until you create a new password.
                                </p>
                                <p>
                                    If you have questions, just reply to this email. We'd be happy to help!
                                </p>
                                <h4 class="footer">
                                    Thanks <b>&</b> Regards<br>
                                    Team Agiliy
                                </h4>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>