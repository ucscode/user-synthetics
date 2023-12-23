<!doctype html>
<html>
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css'>
        <style>
            .codeblock {
                font-family: monospace;
                font-size: 13px;
            }
            li {
                margin-bottom: 1rem;
            }
        </style>
    </head>
    <body>
        <div class='container py-5'>

            <h2 class='mb-3'>
                Vendor Autoload Error!
            </h2>

            <div class='alert alert-danger'>
                This is due to missing "<span class='text-primary'>autoload.php</span>" file in the <span class='text-primary'>vendor/</span> directory
            </div>

            <p>
                To resolve this error, follow the instruction below: 
            </p>

            <hr>

            <div class='bg-light p-4 rounded-2'>
                <ul>    
                    <li>
                        Install <a href='https://getcomposer.org/' target='_blank'>composer</a> (If you don't already have it)
                    </li>
                    <li>
                        Open your <code>Terminal</code> ( or <code>Command Prompt</code> )
                    </li>
                    <li>
                        Go to the project installation directory (as shown below): <br>
                        <div class='text-bg-dark mt-3 p-3 codeblock'>
                            cd <?php echo ROOT_DIR; ?>
                        </div>
                    </li>
                    <li>
                        Then run the following command: <br>
                        <div class='text-bg-dark mt-3 p-3 codeblock'>
                            composer install
                        </div>
                    </li>
                </ul>
            </div>

        </div>
    </body>
</html>