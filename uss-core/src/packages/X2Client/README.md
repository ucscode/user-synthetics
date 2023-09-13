# X2Client: Convert HTML 5 Syntax into Email-Compatible Tables

## Introduction

Sending emails with complex HTML structures can be challenging due to the limited support for HTML 5 syntax in email clients. Instead of writing pure HTML code, developers often resort to using tables to ensure compatibility across multiple email clients, reminiscent of coding practices from the past.

While many email templates are available online, they often require custom modifications to work correctly with your own code. Email clients may strip or override styles, necessitating the use of inline styling and attribute declarations. Additionally, extensive testing is required to ensure compatibility across different email clients, which lack consistent rendering rules.

## X2Client Overview

X2Client is a PHP library designed to ease the process of converting HTML 5 syntax into table-based formats compatible with most email clients. While not strictly HTML 5, X2Client follows a similar coding convention using XML-like syntax with the "x2" prefix for each tag. This makes it easier to read, write, and understand the code.

## How To Use

To use X2Client, follow these steps:

1. Include the X2Client library in your project by adding the following line to your PHP file:

```php
require_once __DIR__ . "/X2Client.php";
```

2. Create an HTML string using the X2 syntax. The X2 syntax follows a similar convention as HTML 5, but with the prefix `x2`. For example:

```php
$EMAIL_STRING = "
  <x2:div>
    <x2:div class='my-class'>
      <x2:p>This is a paragraph</x2:p>
    </x2:div>
  </x2:div>
";
```

3. Instantiate the X2Client class with the HTML string as a parameter:

```php
$X2Client = new X2Client($EMAIL_STRING);
```

4. Use the `render()` method to convert the HTML string into an HTML table:

```php
echo $X2Client->render();
```

## Result

The `render()` method will generate an HTML table suitable for email clients based on the provided HTML string.

### Example Output

```html
<html>
  <head>
    <!-- CSS styles here -->
  </head>
  <body>
    <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="max-width: 100%; table-layout: fixed; word-break: break-word;">
      <!-- Table structure and content here -->
    </table>
  </body>
</html>
```

The resulting HTML table can be used in email templates or other contexts where table-based layouts are commonly supported.

## Features

- Converts internal CSS into inline CSS for better email client compatibility.
- Converts block-level tags, such as `<div>` and `<p>`, into tables or `<td>` elements where applicable.
- Generates properly organized tables with minimal code.
- Adds necessary attributes and styles to ensure compatibility with multiple email clients.
- Reduces the need for extensive testing by providing a streamlined solution.
- Alleviates the frustrations associated with coding email templates.

## Extended Example

X2Client allows you to write HTML 5 syntax with the "x2" prefix before each tag. For example:

```php
$EMAIL_HTML = "
  <x2:html>
    <x2:head>
      <!-- CSS styles here -->
    </x2:head>
    <x2:body>
      <!-- Content here -->
    </x2:body>
  </x2:html>
";
```

The CSS styles and content follow the same structure. After creating the HTML string, use the X2Client instance to render the output:

```php
$X2Client = new X2Client($EMAIL_HTML);
echo $X2Client->render();
```

The resulting output will be an HTML table suitable for email clients.

## API Reference

For more detail, please refer to [this article](https://ucscode.me/?p=1).

Feel free to explore and integrate X2Client into your email coding workflow.
