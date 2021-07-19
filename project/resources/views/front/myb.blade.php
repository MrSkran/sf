@extends('layouts.front')

@section('content')
<!-- Breadcrumb Area Start -->
<div class="breadcrumb-area">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <ul class="pages">
          <li>
            <a href="ddssd">
              fssffssf
            </a>
          </li>
          <li>
            <a href="sdsd">


dssdsd
                
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<body>

<h3>Using CSS to style an HTML Form</h3>
<style>
input[type=text], select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 6px;
  margin-bottom: 16px;
  resize: vertical;
}

input[type=submit] {
  background-color: #4CAF50;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 2px;
  cursor: pointer;
  float: center;
}
input[type=submit]:hover {
  background-color: #45a049;
}

.ssss {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}


</style>
<center>
<div class="ssss">
  <form action="/action_page.php">
    <label for="fname">First Name</label>
    <input type="text" id="fname" name="firstname" placeholder="Your name..">
<div>
  <form>
    <label for="fname">First Name</label>
    <input type="text" id="fname" name="firstname" placeholder="Your name.."><br>

    <label for="lname">Last Name</label>
    <input type="text" id="lname" name="lastname" placeholder="Your last name.."><br>

    <label for="country">Country</label>
    <select id="country" name="country">
      <option value="australia">Australia</option>
      <option value="canada">Canada</option>
      <option value="usa">USA</option>
    </select><br><br>
  
    <input type="submit" value="Submit"><br>
  </form>
</div>
</center>
</body>
@endsection