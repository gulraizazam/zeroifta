<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
            crossorigin="anonymous"
        />
        <!-- Custom Css -->
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />

        <title>ZeroIfta</title>
    </head>
    <body>
        <div class="main">
            <div class="dash-main">
                @if(Auth::user()->role=='admin')
                    @include('includes.sidebar_admin_new')
                @elseif(Auth::user()->role=='company')
                    @include('includes.sidebar_company_new')
                @else
                @endif
                <div class="dashboard-rigt">
                    <div class="container-fluid">
                    @include('includes.header_new')
                    @yield('content')
                    </div>
                </div>
                <!---------------------------------------- Dashboard Right Area End ----------------------------------------->
            </div>
        </div>
         <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"
        ></script>
        <script src="{{asset('assets/chartjs.min.js')}}"></script>
  <script>
    var ctx1 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
    new Chart(ctx1, {
      type: "line",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Companies",
          tension: 0.4,
          borderWidth: 0,
          pointRadius: 0,
          borderColor: "#5e72e4",
          backgroundColor: gradientStroke1,
          borderWidth: 3,
          fill: true,
          data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#fbfbfb',
              font: {
                size: 11,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#ccc',
              padding: 20,
              font: {
                size: 11,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
        <script>
            // Unified function to toggle theme
            function toggleTheme(isDark) {
                // Toggle dark-mode class based on current or passed state
                const darkMode = typeof isDark === "boolean" ? isDark : !document.body.classList.contains("dark-mode");

                document.body.classList.toggle("dark-mode", darkMode);

                // Update visibility of header icons
                document.getElementById("dark-themeIcon").style.display = darkMode ? "none" : "inline-block";
                document.getElementById("light-themeIcon").style.display = darkMode ? "inline-block" : "none";

                // Update sidebar switch state
                document.getElementById("themeCheckbox").checked = darkMode;
            }

            // Event listener for sidebar switch
            document.getElementById("themeCheckbox").addEventListener("change", function () {
                toggleTheme(this.checked);
            });

            // Event listeners for header icons
            document.getElementById("dark-themeIcon").addEventListener("click", function () {
                toggleTheme(true);
            });

            document.getElementById("light-themeIcon").addEventListener("click", function () {
                toggleTheme(false);
            });
        </script>

        <!-- Option 2: Separate Popper and Bootstrap JS -->
        <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    --></body>
</html>