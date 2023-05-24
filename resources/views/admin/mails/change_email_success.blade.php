<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Password Changed Successfully</title>
    <style type="text/css" rel="stylesheet" media="all">
      /* Base ------------------------------ */
      *:not(br):not(tr):not(html) {
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
      }

      body {
        width: 100% !important;
        height: 100%;
        margin: 0;
        line-height: 1.4;
        background-color: #f5f7f9;
        color: #839197;
        -webkit-text-size-adjust: none;
      }

      a {
        color: #1e88e5;
      }

      /* Layout ------------------------------ */
      .email-wrapper {
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: #f5f7f9;
      }

      .email-content {
        width: 100%;
        margin: 0;
        padding: 0;
      }

      /* Masthead ----------------------- */
      .email-masthead {
        padding-top: 50px;
        padding-bottom: 30px;
        text-align: center;
      }

      .email-masthead_logo {
        max-width: 400px;
        border: 0;
      }

      .email-masthead_name {
        font-size: 16px;
        font-weight: bold;
        color: #839197;
        text-decoration: none;
        text-shadow: 0 1px 0 white;
      }

      /* Body ------------------------------ */
      .email-body {
        width: 100%;
        margin: 0;
        padding: 0;
      }

      .email-body_inner {
        width: 650px;
        margin: 0 auto;
        padding: 0;
        border-top: 1px solid #e7eaec;
        border-bottom: 1px solid #e7eaec;
        background: #ffffff;
        box-shadow: 2px 2px 2px 1px #ecebeb;
        border: 1px solid #f1f1f1;
        margin-top: 20px;
      }

      .email-footer {
        width: 650px;
        margin: 0 auto;
        padding: 0;
        text-align: center;
      }

      .email-footer p {
        color: #839197;
      }

      .body-action {
        width: 100%;
        margin: 30px auto;
        padding: 0;
        text-align: center;
      }

      .body-sub {
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #e7eaec;
      }

      .content-cell {
        padding: 25px;
      }

      .align-center {
        text-align: center;
      }

      .align-right {
        text-align: right;
      }

      /* Type ------------------------------ */
      h1 {
        margin-top: 0;
        color: #292e31;
        font-size: 19px;
        font-weight: bold;
        text-align: left;
      }

      h2 {
        margin-top: 0;
        color: #292e31;
        font-size: 16px;
        font-weight: bold;
        text-align: left;
      }

      h3 {
        margin-top: 0;
        color: #292e31;
        font-size: 14px;
        font-weight: bold;
        text-align: left;
      }

      p {
        margin-top: 0;
        color: #727677;
        font-size: 16px;
        line-height: 1.5em;
        text-align: left;
      }

      .body-action tr td {
        padding-top: 4px;
        padding-bottom: 4px;
        padding-left: 10px;
        border: 1px solid #e4e4e4;
        color: #2e2e2e;
        text-align: left;
      }

      /*Media Queries ------------------------------ */
      @media only screen and (max-width: 600px) {
        .email-body_inner,
        .email-footer,
        .email-100 {
          width: 100% !important;
        }
      }

      @media only screen and (max-width: 500px) {
        .button {
          width: 100% !important;
        }
      }
    </style>
  </head>

  <body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
      <tbody>
        <tr>
          <td align="center">
            <table
              class="email-content"
              width="100%"
              cellpadding="0"
              cellspacing="0"
            >
              <!-- Logo -->
              <tbody>
                <!-- Email Body -->
                <tr>
                  <td class="email-body" width="100%">
                    <table
                      class="email-body_inner"
                      align="center"
                      width="650"
                      cellpadding="0"
                      cellspacing="0"
                    >
                      <!-- Body content -->
                      <tbody>
                        <tr>
                          <td
                            style="
                              width: 100%;
                              height: 12px;
                              background: #0086f6;
                            "
                          ></td>
                        </tr>
                        <tr>
                          <td class="email-masthead">
                            <a
                              href="{{opt('company_url')}}"
                              target="_blank"
                              class="email-masthead_name"
                              ><img
                                src="https://img1.wsimg.com/isteam/ip/74c8ac0d-8e9f-4622-b9b6-3b738d6dbb84/EvolutionRx_BlueLogo.png/:/rs=w:343,h:75,cg:true,m/cr=w:343,h:75/qt=q:95"
                                width="260"
                                height=""
                                alt="Logo"
                                style=""
                            /></a>
                          </td>
                        </tr>
                        <tr>
                          <td
                            class="content-cell"
                            style="font-family: Arial, sans-serif"
                          >
                            <h1
                              style="
                                display: block;
                                color: #212b3f;
                                text-align: center;
                                font-size: 40px;
                                margin-bottom: 40px;
                              "
                            >
                              Change Email Details.
                            </h1>

                            <!-- Action -->
                            <table
                              class="body-action"
                              align="center"
                              width="100%"
                              cellpadding="0"
                              cellspacing="0"
                              style="font-family: Arial, sans-serif"
                            >
                              <tbody>
                                <tr>
                                  <td>Subject Id</td>
                                  <td>{{ $user_data['subject_id'] }}</td>
                                </tr>
                                <tr>
                                  <td>Previous Email</td>
                                  <td>
                                    <a href="#">{{ $user_data['old_email'] }}</a>
                                  </td>
                                </tr>
                                <tr>
                                  <td>New Email</td>
                                  <td>
                                    <a href="#">{{ $user_data['new_email'] }}</a>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />

                            <p
                              style="
                                display: block;
                                margin: 0;
                                font-size: 14px;
                                color: #222222;
                                text-align: center;
                              "
                            >
                              ePRO © 2022
                              <a href="{{opt('company_url')}}" style=""
                                >{{opt('site_title')}}</a
                              >
                              - All Rights Reserved.
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
