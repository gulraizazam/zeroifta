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
    crossorigin="anonymous" />
  <!-- Custom Css -->
  <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />
  <!-- Standard Favicon -->
  <link rel="icon" href="{{asset('assets/img/fav-icon.png')}}">

  <title>ZeroIfta</title>
</head>

<body>
  <div class="subs-PlansMain">
    <div class="container-fluid">

      <!-- Header Area -->
      <div class="header-main">
        <div>
          <div class="head-left">
            <svg xmlns="http://www.w3.org/2000/svg" width="162" height="45" viewBox="0 0 162 45" fill="none">
              <path d="M16.8035 10.0822V19.7442L26.4656 4.20093H2.52051V10.0822H16.8035Z" fill="#092E75" />
              <path d="M9.6623 29.8263L9.6623 20.1642L0.000253677 35.7075L23.9453 35.7075L23.9453 29.8263L9.6623 29.8263Z" fill="#092E75" />
              <path d="M37.4145 36.3938C35.3929 36.3938 33.6469 35.9737 32.1766 35.1336C30.715 34.2846 29.5904 33.0856 28.8027 31.5366C28.0151 29.9787 27.6212 28.1452 27.6212 26.036C27.6212 23.9618 28.0151 22.1414 28.8027 20.5749C29.5991 18.9995 30.7106 17.7743 32.1372 16.8991C33.5637 16.0152 35.2397 15.5732 37.1651 15.5732C38.4079 15.5732 39.5806 15.7745 40.6834 16.1771C41.7949 16.5709 42.7751 17.1835 43.624 18.015C44.4817 18.8464 45.1556 19.9054 45.6457 21.1919C46.1358 22.4696 46.3808 23.9925 46.3808 25.7603V27.2175H29.853V24.0143H41.8255C41.8167 23.1042 41.6198 22.2946 41.2347 21.5857C40.8497 20.8681 40.3114 20.3036 39.62 19.8922C38.9374 19.4809 38.141 19.2752 37.2308 19.2752C36.2593 19.2752 35.406 19.5115 34.6708 19.9841C33.9357 20.448 33.3624 21.0606 32.9511 21.822C32.5485 22.5747 32.3429 23.4017 32.3341 24.3032V27.0994C32.3341 28.2721 32.5485 29.2786 32.9774 30.1188C33.4062 30.9502 34.0057 31.5891 34.7759 32.0354C35.546 32.473 36.4475 32.6918 37.4802 32.6918C38.1716 32.6918 38.7973 32.5955 39.3575 32.403C39.9176 32.2017 40.4033 31.9085 40.8146 31.5234C41.226 31.1383 41.5367 30.6614 41.7467 30.0925L46.1839 30.5914C45.9038 31.7641 45.37 32.7881 44.5823 33.6633C43.8034 34.5297 42.8057 35.2036 41.5892 35.6849C40.3727 36.1575 38.9811 36.3938 37.4145 36.3938ZM50.4012 36V15.8357H55.009V19.1965H55.2191C55.5867 18.0325 56.2168 17.1354 57.1095 16.5053C58.0109 15.8664 59.0393 15.5469 60.1945 15.5469C60.4571 15.5469 60.7503 15.5601 61.0741 15.5863C61.4066 15.6038 61.6823 15.6344 61.9011 15.6782V20.0498C61.6998 19.9797 61.3804 19.9185 60.9428 19.866C60.514 19.8047 60.0982 19.7741 59.6957 19.7741C58.8292 19.7741 58.0503 19.9622 57.3589 20.3386C56.6763 20.7061 56.138 21.2181 55.7442 21.8745C55.3504 22.5309 55.1535 23.2879 55.1535 24.1456V36H50.4012ZM73.0236 36.3938C71.0545 36.3938 69.3479 35.9606 67.9038 35.0942C66.4597 34.2278 65.3395 33.0156 64.5431 31.4578C63.7554 29.9 63.3616 28.0796 63.3616 25.9966C63.3616 23.9137 63.7554 22.0889 64.5431 20.5224C65.3395 18.9558 66.4597 17.7393 67.9038 16.8728C69.3479 16.0064 71.0545 15.5732 73.0236 15.5732C74.9928 15.5732 76.6994 16.0064 78.1435 16.8728C79.5875 17.7393 80.7034 18.9558 81.491 20.5224C82.2875 22.0889 82.6857 23.9137 82.6857 25.9966C82.6857 28.0796 82.2875 29.9 81.491 31.4578C80.7034 33.0156 79.5875 34.2278 78.1435 35.0942C76.6994 35.9606 74.9928 36.3938 73.0236 36.3938ZM73.0499 32.5868C74.1176 32.5868 75.0103 32.2936 75.728 31.7072C76.4456 31.1121 76.9795 30.3157 77.3295 29.318C77.6884 28.3203 77.8678 27.2088 77.8678 25.9835C77.8678 24.7495 77.6884 23.6336 77.3295 22.6359C76.9795 21.6295 76.4456 20.8287 75.728 20.2335C75.0103 19.6384 74.1176 19.3409 73.0499 19.3409C71.9559 19.3409 71.0457 19.6384 70.3193 20.2335C69.6017 20.8287 69.0634 21.6295 68.7046 22.6359C68.3545 23.6336 68.1795 24.7495 68.1795 25.9835C68.1795 27.2088 68.3545 28.3203 68.7046 29.318C69.0634 30.3157 69.6017 31.1121 70.3193 31.7072C71.0457 32.2936 71.9559 32.5868 73.0499 32.5868Z" fill="#092E75" />
              <path d="M142.244 36.1276H137.045L146.51 9.24194H152.523L162.001 36.1276H156.802L149.621 14.7556H149.411L142.244 36.1276ZM142.414 25.586H156.592V29.4981H142.414V25.586Z" fill="#092E75" />
              <path d="M116.454 13.3247V9.24194H137.905V13.3247H129.595V36.1276H124.764V13.3247H116.454Z" fill="#092E75" />
              <path d="M95.9746 36.1276V9.24194H114.879V13.3247H100.845V20.6237H117.898V24.7065H100.845V36.1276H95.9746Z" fill="#092E75" />
              <path d="M90.7005 9.24194V36.1276H85.8301V9.24194H90.7005Z" fill="#092E75" />
            </svg>
          </div>
        </div>
        <div class="right-opts">
          <div class="head-right">
            <div class="search-div">
              <div class="serch-tab">
                <input type="text" placeholder="Type Here" name="" id="">
                <span class="s-icon hs-svg">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                    <circle cx="5" cy="5" r="4.3" stroke="" stroke-width="1.4"></circle>
                    <line x1="10.0101" y1="11" x2="8" y2="8.98995" stroke="" stroke-width="1.4" stroke-linecap="round"></line>
                  </svg>
                </span>
              </div>
            </div>
            <div class="opt-div">
              <div class="mob-menu">
                <div class="mobLogo-div">
                  <img src="assets/img/logo-blue.png" alt="ZeroIfta Logo">
                </div>
              </div>
              <div class="menu-opt">
                <div id="dark-themeIcon" class="dark-themeIcon hf-svg">
                  <svg xmlns="http://www.w3.org/2000/svg" width="17" height="18" viewBox="0 0 17 18" fill="none">
                    <path d="M9 18C11.776 18 14.3114 16.737 15.9911 14.6675C16.2396 14.3613 15.9686 13.9141 15.5846 13.9872C11.2181 14.8188 7.20819 11.4709 7.20819 7.06303C7.20819 4.52398 8.5674 2.18914 10.7765 0.931992C11.117 0.738211 11.0314 0.221941 10.6444 0.150469C10.102 0.0504468 9.55158 8.21369e-05 9 0C4.03211 0 0 4.02578 0 9C0 13.9679 4.02578 18 9 18Z" fill=""></path>
                  </svg>
                </div>
                <div id="light-themeIcon" class="light-themeIcon hf-svg" style="display: none">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4V1H13V4H11ZM11 23V20H13V23H11ZM20 13V11H23V13H20ZM1 13V11H4V13H1ZM18.7 6.7L17.3 5.3L19.05 3.5L20.5 4.95L18.7 6.7ZM4.95 20.5L3.5 19.05L5.3 17.3L6.7 18.7L4.95 20.5ZM19.05 20.5L17.3 18.7L18.7 17.3L20.5 19.05L19.05 20.5ZM5.3 6.7L3.5 4.95L4.95 3.5L6.7 5.3L5.3 6.7ZM12 18C10.3333 18 8.91667 17.4167 7.75 16.25C6.58333 15.0833 6 13.6667 6 12C6 10.3333 6.58333 8.91667 7.75 7.75C8.91667 6.58333 10.3333 6 12 6C13.6667 6 15.0833 6.58333 16.25 7.75C17.4167 8.91667 18 10.3333 18 12C18 13.6667 17.4167 15.0833 16.25 16.25C15.0833 17.4167 13.6667 18 12 18ZM12 16C13.1167 16 14.0625 15.6125 14.8375 14.8375C15.6125 14.0625 16 13.1167 16 12C16 10.8833 15.6125 9.9375 14.8375 9.1625C14.0625 8.3875 13.1167 8 12 8C10.8833 8 9.9375 8.3875 9.1625 9.1625C8.3875 9.9375 8 10.8833 8 12C8 13.1167 8.3875 14.0625 9.1625 14.8375C9.9375 15.6125 10.8833 16 12 16Z" fill=""></path>
                  </svg>
                </div>
                <div class="hf-svg">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20" fill="none">
                    <path d="M15.2901 15.29L14.0001 14V9C14.0001 5.93 12.3601 3.36 9.50005 2.68V2C9.50005 1.17 8.83005 0.5 8.00005 0.5C7.17005 0.5 6.50005 1.17 6.50005 2V2.68C3.63005 3.36 2.00005 5.92 2.00005 9V14L0.710051 15.29C0.0800515 15.92 0.520051 17 1.41005 17H14.5801C15.4801 17 15.9201 15.92 15.2901 15.29ZM12.0001 15H4.00005V9C4.00005 6.52 5.51005 4.5 8.00005 4.5C10.4901 4.5 12.0001 6.52 12.0001 9V15ZM8.00005 20C9.10005 20 10.0001 19.1 10.0001 18H6.00005C6.00005 19.1 6.89005 20 8.00005 20Z" fill=""></path>
                  </svg>
                </div>
                <div class="up-img">
                  <div class="dropdown">
                    @if(Auth::user() && Auth::user()->image)
                    <img src="{{asset('images')}}/{{Auth::user()->image}}" alt="ZeroIfta Image" onclick="toggleDropdown()" style="height: 30px;border-radius:100%" />
                    @else
                    <img src="{{asset('assets/img/user-img.png')}}" alt="ZeroIfta Image" onclick="toggleDropdown()" />
                    @endif
                    <div id="myDropdown" class="dropdown-content">

                      <div class="hdd-inn">
                        <div>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="none">
                            <path d="M13.5625 11.4375H12.4641C12.3891 11.4375 12.3188 11.4703 12.2719 11.5281C12.1625 11.661 12.0453 11.7891 11.9219 11.911C11.417 12.4163 10.819 12.8191 10.1609 13.0969C9.47915 13.3849 8.74636 13.5326 8.00625 13.5313C7.25781 13.5313 6.53281 13.3844 5.85156 13.0969C5.19347 12.8191 4.59547 12.4163 4.09063 11.911C3.58487 11.4073 3.1816 10.8103 2.90313 10.1531C2.61406 9.4719 2.46875 8.74846 2.46875 8.00002C2.46875 7.25159 2.61563 6.52815 2.90313 5.8469C3.18125 5.18909 3.58125 4.5969 4.09063 4.08909C4.6 3.58127 5.19219 3.18127 5.85156 2.90315C6.53281 2.61565 7.25781 2.46877 8.00625 2.46877C8.75469 2.46877 9.47969 2.61409 10.1609 2.90315C10.8203 3.18127 11.4125 3.58127 11.9219 4.08909C12.0453 4.21252 12.1609 4.34065 12.2719 4.4719C12.3188 4.52971 12.3906 4.56252 12.4641 4.56252H13.5625C13.6609 4.56252 13.7219 4.45315 13.6672 4.37034C12.4688 2.50784 10.3719 1.27502 7.98906 1.28127C4.24531 1.29065 1.24375 4.32971 1.28125 8.06877C1.31875 11.7485 4.31563 14.7188 8.00625 14.7188C10.3828 14.7188 12.4703 13.4875 13.6672 11.6297C13.7203 11.5469 13.6609 11.4375 13.5625 11.4375ZM14.9516 7.90159L12.7344 6.15159C12.6516 6.08596 12.5312 6.14534 12.5312 6.25002V7.43752H7.625C7.55625 7.43752 7.5 7.49377 7.5 7.56252V8.43752C7.5 8.50627 7.55625 8.56252 7.625 8.56252H12.5312V9.75002C12.5312 9.85471 12.6531 9.91409 12.7344 9.84846L14.9516 8.09846C14.9665 8.08677 14.9786 8.07183 14.9869 8.05477C14.9952 8.03772 14.9995 8.019 14.9995 8.00002C14.9995 7.98105 14.9952 7.96233 14.9869 7.94527C14.9786 7.92822 14.9665 7.91328 14.9516 7.90159Z" />
                          </svg>
                        </div>
                        <div>
                          <a href="{{route('logout')}}"><span>Logout</span></a>
                        </div>

                      </div>

                    </div>
                  </div>
                </div>
                <div class="mob-menu">
                  <div id="hamburgerIcon" class="hamburger-icon">
                    ☰
                    <!-- Simple hamburger icon, you can replace this with an SVG or icon font -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Subscription Row -->
      <div class="user-subsPlnas">
        <div class="usp_inn">
          <div class="manage-comp mb-4">
            <div class="Filters-main mb-3 mb-md-4">
              <div class="sec1-style">
                <div class="text-center inHead-span">
                  <h2 class="head-20Med">Subscription Plans</h2>
                </div>
                <div class="plans-toggel">
                  <div class="sub-toggel text-center">
                    <div>
                      <p>Monthly</p>
                    </div>
                    <div>
                      <label class="switch">
                        <input type="checkbox" id="plansSwitchCheckbox">
                        <span class="slider round"></span>
                      </label>
                    </div>
                    <div>
                      <div>
                        <p>Yearly</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="subs_plan">

                  <!-- Monthly Plan -->
                  <div id="monthly_plans" class="monthly_plans">
                    <div class="row">
                      <!-- Plan 1 -->
                      <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                        <div class="price_plans sbsp-com weekly-plan">
                          <div class="ph-area">
                            <h3>Free Trial</h3>
                            <p> Trial Plan (6 Month Only)</p>
                          </div>
                          <div class="sbp-section">
                            <div>
                              <p class="">
                                <span class="usp-head">You are availing all the features of the</span>
                                <span class="usp-ys"> <b>Premium Monthly Plan</b> </span>
                                <span class="usp-head">for 6 months.</span>

                              </p>
                            </div>
                            <div class="pt-4">
                              <ul>
                                <li class="sbp-list mb-3">
                                  <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 28 28" fill="none">
                                      <g clip-path="url(#clip0_2807_1039)">
                                        <path d="M26.1395 2.69597C25.6332 2.49558 25.0584 2.62742 24.6893 3.02292L19.5107 9.64109L15.1338 1.14558C14.8965 0.7448 14.4693 0.496948 14 0.496948C13.5307 0.496948 13.0982 0.7448 12.8662 1.14558L8.48926 9.64109L3.31074 3.0282C2.9416 2.63269 2.3668 2.50085 1.86055 2.70125C1.35957 2.89636 1.02734 3.38152 1.02734 3.92468V21.4852C1.02734 23.3731 2.56719 24.913 4.45508 24.913H23.5449C25.4328 24.913 26.9727 23.3731 26.9727 21.4852V3.92468C26.9727 3.38152 26.6404 2.89636 26.1395 2.69597Z" fill="#FFBC13" />
                                      </g>
                                      <defs>
                                        <clipPath id="clip0_2807_1039">
                                          <rect width="27" height="27" fill="white" transform="translate(0.5 0.0487061)" />
                                        </clipPath>
                                      </defs>
                                    </svg>
                                  </span>
                                  <div>
                                    <p class="">
                                      <span class="usp-head">
                                        After the trial period, the subscription will automatically roll into the
                                      </span>
                                      <br>
                                      <span class="usp-ys blink_me">
                                        premium plan at $67 per month
                                      </span>

                                    </p>
                                  </div>
                                </li>
                              </ul>
                            </div>
                            <div class="text-center mb-5 mt-3">
                              <p class="usp-para">
                                Invest in the Premium + Yearly Subscription today and experience the complete power of ZeroIFTA!
                              </p>
                            </div>

                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','free_trial')}}" class="mainBtn">
                               Start Free Trial
                              </a>
                            </div>

                          </div>

                        </div>
                      </div>
                      <!-- Plan 2 -->
                      <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                        <div class="price_plans sbsp-com weekly-plan">
                          <div class="ph-area">
                            <h3>Basic Plan</h3>
                            <p> $55 - Monthly</p>
                          </div>
                          <div class="sbp-section">
                            <div>
                              <h4 class="pb-3">Features</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fuel Station Recommendations</p>
                                      <p class="usp-para">Get the best fuel stops based on the unburdened fuel price to maximize savings.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Monthly Savings and IFTA Reports</p>
                                      <p class="usp-para">Review your savings each month to track how much you've kept in your pocket.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Customize odometer reading and truck MPG's</p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div class="pt-5">
                              <h4 class="pb-3">What's Not Included:</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.0934 10.0146L5.92676 5.8479M10.0934 10.0146L14.2601 14.1812M10.0934 10.0146L14.2601 5.8479M10.0934 10.0146L5.92676 14.1812" stroke="#B60F0F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">No live vehicle tracking or alerts.</p>
                                      <p class="usp-para">Get the best fuel stops based on the unburdened fuel price to maximize savings.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.0934 10.0146L5.92676 5.8479M10.0934 10.0146L14.2601 14.1812M10.0934 10.0146L14.2601 5.8479M10.0934 10.0146L5.92676 14.1812" stroke="#B60F0F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-para">No customization of app features, such as setting a minimum number of gallons to fuel, adding stops to trips, or change the default reserve fuel amount.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.0934 10.0146L5.92676 5.8479M10.0934 10.0146L14.2601 14.1812M10.0934 10.0146L14.2601 5.8479M10.0934 10.0146L5.92676 14.1812" stroke="#B60F0F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-para">Can't customize fuel tank capacity </p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','basic_monthly')}}" class="mainBtn">
                              Start Basic Plan
                              </a>
                            </div>
                          </div>

                        </div>
                      </div>
                      <!-- Plan 3 -->
                      <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                        <div class="price_pm sbsp-com montly-plan top-plan">
                          <div class="pop-div">
                            <span>Recommended</span>
                          </div>
                          <div class="ph-area">
                            <h3 style="color:#0c388b;">Premium Plan</h3>
                            <p> $67 - Monthly</p>
                          </div>
                          <div class="sbp-section">
                            <!-- Sec 1 -->
                            <div>
                              <h4 class="pb-3">Features</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">All Features of the Basic Plan.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Automatic IFTA Reporting: </p>
                                      <p class="usp-para">Simplify compliance with automated tax reporting.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fully Customizable App and Features:</p>
                                      <p class="usp-para">
                                        <b>Tailor the app to your needs.</b>
                                      </p>
                                      <ul class="pt-2">
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <!-- <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#19A130">
                                            <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                          </svg> -->
                                            <p>
                                              <b>1.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Adjust settings like minimum gallons to fuel,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>2.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Add unlimited stops to trips,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>3.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Change the default reserve fuel amount,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>4.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Customize odometer reading and truck MPG's,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>5.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Customizable alerts (choose at wish preferred distances to get notified of your upcoming stop)
                                            </p>
                                          </div>
                                        </li>
                                      </ul>

                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Live Vehicle Tracking: </p>
                                      <p class="usp-para">Monitor your fleet in real-time and track your trucks' positions.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Alerts and Notifications: </p>
                                      <p class="usp-para">Receive alerts if drivers miss suggested fuel stops or deviate from planned routes.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Advanced Trip Planning and Analytics: </p>
                                      <p class="usp-para">Optimize routes with predictive fuel pricing, weather, traffic behavior and vehicle performance data.</p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>


                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','premium_monthly')}}" class="mainBtn">
                              Start Premium Plan
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Plan 4 -->
                      <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                        <div class="price_plans sbsp-com weekly-plan">
                          <div class="ph-area">
                            <h3>Premium+ Subscription </h3>
                            <p> $97 - Monthly</p>
                          </div>
                          <div class="sbp-section">
                            <div>
                              <p class="usp-para pb-2">
                                Our Premium+ Subscription offers the most comprehensive set of features designed to elevate your
                                fleet management experience. This plan includes everything from our Basic and Premium plans,
                                plus advanced integrations to maximize efficiency and streamline operations.
                              </p>
                              <h4 class="pb-3">What's Included</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fuel Station Recommendations</p>
                                      <p class="usp-para">Based on unburdened fuel prices for smarter fueling decisions.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Monthly Savings Reports</p>
                                      <p class="usp-para"> Track your savings and optimize your costs.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Automatic IFTA Reporting</p>
                                      <p class="usp-para">Eliminate audits and simplify tax compliance with automated reporting.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Live Vehicle Tracking and Alerts</p>
                                      <p class="usp-para">Real-time tracking of all trucks, with alerts for missed fuel stops and deviations from optimized routes.</p>
                                    </div>
                                  </li>

                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fully Customizable App</p>
                                      <p class="usp-para">Set your minimum fueling amounts, add stops, and personalize features to fit your unique needs.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Admin Panel API Integration</p>
                                      <p class="usp-para">
                                        Sync your existing fleet software with ZeroIFTA. All truck, driver,
                                        and essential fleet information is automatically shared and
                                        updated without manual input, ensuring a streamlined workflow between systems.
                                      </p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Real-Time Data Sync</p>
                                      <p class="usp-para">
                                        Maintain accurate fleet information with automatic updates, helping you keep track of crucial data effortlessly.
                                      </p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','premium_plus_monthly')}}" class="mainBtn">
                              Start Premium+ Plan
                              </a>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Yearly Plan -->
                  <div id="yearly_plans" class="yearly_plans" style="display: none;">
                    <div class="text-center mb-5 mt-3">
                      <p>
                        Invest in the Premium + Yearly Subscription today and experience the complete power of ZeroIFTA!
                      </p>
                    </div>
                    <div class="row">
                      <!-- Plan 1 -->
                      <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                        <div class="price_plans sbsp-com weekly-plan">
                          <div class="ph-area">
                            <h3>Free Trial</h3>
                            <p style="color:#0c388b;"> Trial Plan (6 Month Only)</p>
                          </div>
                          <div class="sbp-section">
                            <div>
                              <p class="">
                                <span class="usp-head">You are availing all the features of the</span>
                                <span class="usp-ys"> <b>Premium Monthly Plan</b> </span>
                                <span class="usp-head">for 6 months.</span>

                              </p>
                            </div>
                            <div class="pt-4">
                              <ul>
                                <li class="sbp-list mb-3">
                                  <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 28 28" fill="none">
                                      <g clip-path="url(#clip0_2807_1039)">
                                        <path d="M26.1395 2.69597C25.6332 2.49558 25.0584 2.62742 24.6893 3.02292L19.5107 9.64109L15.1338 1.14558C14.8965 0.7448 14.4693 0.496948 14 0.496948C13.5307 0.496948 13.0982 0.7448 12.8662 1.14558L8.48926 9.64109L3.31074 3.0282C2.9416 2.63269 2.3668 2.50085 1.86055 2.70125C1.35957 2.89636 1.02734 3.38152 1.02734 3.92468V21.4852C1.02734 23.3731 2.56719 24.913 4.45508 24.913H23.5449C25.4328 24.913 26.9727 23.3731 26.9727 21.4852V3.92468C26.9727 3.38152 26.6404 2.89636 26.1395 2.69597Z" fill="#FFBC13" />
                                      </g>
                                      <defs>
                                        <clipPath id="clip0_2807_1039">
                                          <rect width="27" height="27" fill="white" transform="translate(0.5 0.0487061)" />
                                        </clipPath>
                                      </defs>
                                    </svg>
                                  </span>
                                  <div>
                                    <p class="">
                                      <span class="usp-head">
                                        After the trial period, the subscription will automatically roll into the
                                      </span>
                                      <br>
                                      <span class="usp-ys blink_me">
                                        premium plan at $67 per month
                                      </span>

                                    </p>
                                  </div>
                                </li>
                              </ul>
                            </div>
                            <div class="text-center mb-5 mt-3">
                              <p class="usp-para">
                                Invest in the Premium + Yearly Subscription today and experience the complete power of ZeroIFTA!
                              </p>
                            </div>

                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','free_trial')}}" class="mainBtn">
                              Start Free Trial
                              </a>
                            </div>
                          </div>

                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                        <div class="price_plans sbsp-com weekly-plan">
                          <div class="ph-area">
                            <h3>Basic Plan</h3>
                            <p> $595 - Year</p>
                            <span class="pt-1 usp-para">Equivalent to $49.58 per month (9.85% discount)</span>
                          </div>
                          <div class="sbp-section">
                            <div>
                              <h4 class="pb-3">Features</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fuel Station Recommendations</p>
                                      <p class="usp-para">Get the best fuel stops based on the unburdened fuel price to maximize savings.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Monthly Savings and IFTA Reports</p>
                                      <p class="usp-para">Review your savings each month to track how much you've kept in your pocket.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Customize odometer reading and truck MPG's</p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div class="pt-5">
                              <h4 class="pb-3">What's Not Included:</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.0934 10.0146L5.92676 5.8479M10.0934 10.0146L14.2601 14.1812M10.0934 10.0146L14.2601 5.8479M10.0934 10.0146L5.92676 14.1812" stroke="#B60F0F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">No live vehicle tracking or alerts.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.0934 10.0146L5.92676 5.8479M10.0934 10.0146L14.2601 14.1812M10.0934 10.0146L14.2601 5.8479M10.0934 10.0146L5.92676 14.1812" stroke="#B60F0F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-para">No customization of app features, such as setting a minimum number of gallons to fuel, adding stops to trips, or change the default reserve fuel amount.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M10.0934 10.0146L5.92676 5.8479M10.0934 10.0146L14.2601 14.1812M10.0934 10.0146L14.2601 5.8479M10.0934 10.0146L5.92676 14.1812" stroke="#B60F0F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-para">Can't customize fuel tank capacity </p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','basic_yearly')}}" class="mainBtn">
                                Start Basic Plan
                              </a>
                            </div>
                          </div>

                        </div>
                      </div>
                      <!-- Plan 2 -->
                      <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                        <div class="price_pm sbsp-com montly-plan top-plan">
                          <div class="pop-div">
                            <span>Recommended</span>
                          </div>
                          <div class="ph-area">
                            <h3 style="color:#0c388b;">Premium Plan</h3>
                            <p> $730 - Year</p>
                            <span class="pt-1 usp-para">Equivalent to $60.83 per month (9.21% discount)</span>
                          </div>
                          <div class="sbp-section">
                            <!-- Sec 1 -->
                            <div>
                              <h4 class="pb-3">Features</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">All Features of the Basic Plan.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Automatic IFTA Reporting: </p>
                                      <p class="usp-para">Simplify compliance with automated tax reporting.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fully Customizable App and Features:</p>
                                      <p class="usp-para">
                                        <b>Tailor the app to your needs.</b>
                                      </p>
                                      <ul class="pt-2">
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <!-- <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#19A130">
                                            <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                          </svg> -->
                                            <p>
                                              <b>1.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Adjust settings like minimum gallons to fuel,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>2.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Add unlimited stops to trips,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>3.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Change the default reserve fuel amount,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>4.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Customize odometer reading and truck MPG's,
                                            </p>
                                          </div>
                                        </li>
                                        <li class="sbp-list mb-3">
                                          <span style="color: #19A130">
                                            <p>
                                              <b>5.</b>
                                            </p>
                                          </span>
                                          <div>
                                            <p class="usp-para">
                                              Customizable alerts (choose at wish preferred distances to get notified of your upcoming stop)
                                            </p>
                                          </div>
                                        </li>
                                      </ul>

                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Live Vehicle Tracking: </p>
                                      <p class="usp-para">Monitor your fleet in real-time and track your trucks' positions.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Alerts and Notifications: </p>
                                      <p class="usp-para">Receive alerts if drivers miss suggested fuel stops or deviate from planned routes.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Advanced Trip Planning and Analytics: </p>
                                      <p class="usp-para">Optimize routes with predictive fuel pricing, weather, traffic behavior and vehicle performance data.</p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>


                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','premium_yearly')}}" class="mainBtn">
                                Start Premium Plan
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Plan 3 -->
                      <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                        <div class="price_plans sbsp-com weekly-plan">
                          <div class="ph-area">
                            <h3>Premium+ Subscription </h3>
                            <p> $995 - Year</p>
                            <span class="pt-1 usp-para">Equivalent to $82.91 per month (14.53% discount)</span>
                          </div>
                          <div class="sbp-section">
                            <div>
                              <p class="usp-para pb-2">
                                The Premium+ Yearly Subscription gives you full access to all of ZeroIFTA's most
                                powerful features at a discounted rate, offering the ultimate solution for
                                fleet management and fuel optimization.
                              </p>
                              <h4 class="pb-3">What's Included</h4>
                              <div class="pp-inn">
                                <ul>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fuel Station Recommendations:</p>
                                      <p class="usp-para">Based on unburdened fuel prices for smarter fueling decisions.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Monthly Savings Reports:</p>
                                      <p class="usp-para"> Track your savings and optimize your costs.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Automatic IFTA Reporting:</p>
                                      <p class="usp-para">Eliminate audits and simplify tax compliance with automated reporting.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Live Vehicle Tracking and Alerts:</p>
                                      <p class="usp-para">Monitor your fleet in real-time and receive alerts for missed stops or route changes.</p>
                                    </div>
                                  </li>

                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Fully Customizable App:</p>
                                      <p class="usp-para">Adjust settings like minimum gallons to fuel, add stops, and tailor the app to fit your needs.</p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Advanced Trip Planning and Analytics:</p>
                                      <p class="usp-para">
                                        Utilize predictive fuel pricing, weather data, and vehicle performance insights for efficient route planning.
                                      </p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">API Integration with Admin Panel:</p>
                                      <p class="usp-para">
                                        Seamlessly connect your software with ZeroIFTA, ensuring all truck, driver, and crucial fleet information is automatically shared and synced without manual input.
                                      </p>
                                    </div>
                                  </li>
                                  <li class="sbp-list mb-3">
                                    <span>
                                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="23" viewBox="0 0 26 23" fill="none">
                                        <path d="M6.26598 19.073C5.80585 19.073 5.41195 18.8989 5.08428 18.5508C4.75661 18.2026 4.59277 17.7841 4.59277 17.2952V4.85078C4.59277 4.36189 4.75661 3.94337 5.08428 3.59522C5.41195 3.24707 5.80585 3.073 6.26598 3.073H17.9784C18.0899 3.073 18.1945 3.08411 18.2921 3.10633C18.3897 3.12855 18.4873 3.16189 18.5849 3.20633L17.0372 4.85078H6.26598V17.2952H17.9784V11.3841L19.6516 9.60633V17.2952C19.6516 17.7841 19.4878 18.2026 19.1601 18.5508C18.8324 18.8989 18.4385 19.073 17.9784 19.073H6.26598ZM11.7248 15.5174L6.998 10.4952L8.16924 9.25078L11.7248 13.0286L19.4006 4.873L20.5928 6.09522L11.7248 15.5174Z" fill="#19A130" />
                                      </svg>
                                    </span>
                                    <div>
                                      <p class="usp-head">Special Yearly Rate: $995:</p>
                                      <p class="usp-para">
                                        Secure the full suite of Premium+ features for a whole year and save over 15% compared to the monthly plan.
                                        Enjoy seamless integration, powerful analytics, and advanced tools designed to
                                        maximize your fleet's efficiency and keep your business running smoothly.
                                      </p>
                                    </div>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <!-- Button -->
                            <div class="palns-btn mt-5">
                              <a href="{{route('buy','premium_plus_yearly')}}" class="mainBtn">
                                Start Premium+ Plan
                              </a>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>


                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
<script src="{{asset('assets/js/script.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
  $("#plansSwitchCheckbox").on('change', function() {
    if ($(this).prop('checked')) {
      $("#yearly_plans").show();
      $("#monthly_plans").hide();
    } else {
      $("#yearly_plans").hide();
      $("#monthly_plans").show();
    }
  });
</script>


</html>
