

<h3>UK VPN Procedure</h3>

    <p>To add a VPN, Go to the customer and add new service:</p>
    <p><img src="procedures/ukVPNprocedure/hecmdfbahjkckemg.png" alt=""></p>
    <p>On the new service select INT as service type, put 15mbps for
      speed, vlan 1005 - VPN UK, and make sure the wifi suggested name
      is the same as the main one, and then add "_uk" at the end of the
      ssid so the customer knows this one is the uk one. This one had
      LazerPrimavera as the main wifi, so the uk one should also be
      LazerPrimavera_uk. Keep the same password as the main one as the
      default value. press add.<br>
    </p>
    <p><img src="procedures/ukVPNprocedure/nmfajhbpmkppfedn.png" alt=""></p>
    <p><br>
    </p>
    <p>After this, you should have the new internet service in (*this is
      another customer with vpn):</p>
    <p><img src="procedures/ukVPNprocedure/cgnkjjfkfokgifpb.png" alt=""></p>
    <p>OLT provisionning commands will look like this:</p>
    <p><img src="procedures/ukVPNprocedure/flbknfgljhklaobc.png" alt=""></p>
    <p>Provisioning</p>
    <p>By default we want to add a new wifi for this uk internet, and
      ethernet port 4.</p>
    <p>Always check the port configuration of the ONT before moving any
      further. <br>
    </p>
    <p><img src="procedures/ukVPNprocedure/plcembjnlgjbkplg.png" alt=""></p>
    <p> you can confirm port usage on the webpage of the ont
      /status/interfaces/lan. in this example, port 4 is in use, so we
      will not provision this port, and we'll wait for the customer to
      request it.<br>
    </p>
    <p> <img src="procedures/ukVPNprocedure/pngjnklkobhpabma.png" alt=""></p>
    <p><br>
    </p>
    <p>In case the port is free, you need to delete the existing bridge
      port to release the port.</p>
    <span style="color: rgb(0, 0, 0); font-family: CenturyGothicStd,
      &quot;Century Gothic&quot;, CenturyGothic; font-size: medium;
      font-style: normal; font-variant-ligatures: normal;
      font-variant-caps: normal; font-weight: 400; letter-spacing:
      normal; orphans: 2; text-align: start; text-indent: 0px;
      text-transform: none; white-space: normal; widows: 2;
      word-spacing: 0px; -webkit-text-stroke-width: 0px;
      background-color: rgb(254, 254, 254); text-decoration-thickness:
      initial; text-decoration-style: initial; text-decoration-color:
      initial; display: inline !important; float: none;">bridge delete 
      <u>1/11/4/6</u>/gpononu <u>vlan 5</u> <u>eth 4</u></span>
    <p>then you can provision that port with the new bridge and wlan 2.
      Commands need to be adjusted to reflect this. Also if the ONT has
      5GHZ radios (model 2727 and 2428) you should include wlan 6 as
      well. Usually the commands are: <br>
    </p>
    <p>(notice that gem needs to be added 200 of the previous bridge,
      gems have to be 900, 1100 or 1300 +onu id).<br>
    </p>
    <p><b><span style="color: rgb(0, 0, 0); font-family:
          CenturyGothicStd, &quot;Century Gothic&quot;, CenturyGothic;
          font-size: medium; font-style: normal; font-variant-ligatures:
          normal; font-variant-caps: normal; letter-spacing: normal;
          text-align: start; text-indent: 0px; text-transform: none;
          white-space: normal; word-spacing: 0px;
          -webkit-text-stroke-width: 0px; background-color: rgb(254,
          254, 254); text-decoration-thickness: initial;
          text-decoration-style: initial; text-decoration-color:
          initial; display: inline !important; float: none;">bridge add
          1/11/4/6/gpononu <u>gem 1106</u> gtp 3 epktrule 3 ipktrule 3
          downlink vlan 1005 tagged eth 4 rg-brouted</span></b></p>
    <p><b><span style="color: rgb(0, 0, 0); font-family:
          CenturyGothicStd, &quot;Century Gothic&quot;, CenturyGothic;
          font-size: medium; font-style: normal; font-variant-ligatures:
          normal; font-variant-caps: normal; letter-spacing: normal;
          text-align: start; text-indent: 0px; text-transform: none;
          white-space: normal; word-spacing: 0px;
          -webkit-text-stroke-width: 0px; background-color: rgb(254,
          254, 254); text-decoration-thickness: initial;
          text-decoration-style: initial; text-decoration-color:
          initial; display: inline !important; float: none;"><span
            style="color: rgb(0, 0, 0); font-family: CenturyGothicStd,
            &quot;Century Gothic&quot;, CenturyGothic; font-size:
            medium; font-style: normal; font-variant-ligatures: normal;
            font-variant-caps: normal; letter-spacing: normal;
            text-align: start; text-indent: 0px; text-transform: none;
            white-space: normal; word-spacing: 0px;
            -webkit-text-stroke-width: 0px; background-color: rgb(254,
            254, 254); text-decoration-thickness: initial;
            text-decoration-style: initial; text-decoration-color:
            initial; display: inline !important; float: none;">bridge
            add 1/11/4/6/gpononu <u>gem 1106</u> gtp 3 epktrule 3
            ipktrule 3 downlink vlan 1005 tagged <u>wlan 2</u>
            rg-brouted</span></span></b></p>
    <p><b><span style="color: rgb(0, 0, 0); font-family:
          CenturyGothicStd, &quot;Century Gothic&quot;, CenturyGothic;
          font-size: medium; font-style: normal; font-variant-ligatures:
          normal; font-variant-caps: normal; letter-spacing: normal;
          text-align: start; text-indent: 0px; text-transform: none;
          white-space: normal; word-spacing: 0px;
          -webkit-text-stroke-width: 0px; background-color: rgb(254,
          254, 254); text-decoration-thickness: initial;
          text-decoration-style: initial; text-decoration-color:
          initial; display: inline !important; float: none;"><span
            style="color: rgb(0, 0, 0); font-family: CenturyGothicStd,
            &quot;Century Gothic&quot;, CenturyGothic; font-size:
            medium; font-style: normal; font-variant-ligatures: normal;
            font-variant-caps: normal; letter-spacing: normal;
            text-align: start; text-indent: 0px; text-transform: none;
            white-space: normal; word-spacing: 0px;
            -webkit-text-stroke-width: 0px; background-color: rgb(254,
            254, 254); text-decoration-thickness: initial;
            text-decoration-style: initial; text-decoration-color:
            initial; display: inline !important; float: none;"><span
              style="color: rgb(0, 0, 0); font-family: CenturyGothicStd,
              &quot;Century Gothic&quot;, CenturyGothic; font-size:
              medium; font-style: normal; font-variant-ligatures:
              normal; font-variant-caps: normal; letter-spacing: normal;
              text-align: start; text-indent: 0px; text-transform: none;
              white-space: normal; word-spacing: 0px;
              -webkit-text-stroke-width: 0px; background-color: rgb(254,
              254, 254); text-decoration-thickness: initial;
              text-decoration-style: initial; text-decoration-color:
              initial; display: inline !important; float: none;">bridge
              add 1/11/4/6/gpononu <u>gem 1106</u> gtp 3 epktrule 3
              ipktrule 3 downlink vlan 1005 tagged <u>wlan 6</u>
              rg-brouted</span></span></span></b></p>
    <p> <br>
    </p>
    <p>Then we need to create the wifi. Because this is a virtual wifi
      on top of the existing physical wifi, this one should not have
      profiles.<br>
    </p>
    <p><b><span style="color: rgb(0, 0, 0); font-family:
          CenturyGothicStd, &quot;Century Gothic&quot;, CenturyGothic;
          font-size: medium; font-style: normal; font-variant-ligatures:
          normal; font-variant-caps: normal; letter-spacing: normal;
          text-align: start; text-indent: 0px; text-transform: none;
          white-space: normal; word-spacing: 0px;
          -webkit-text-stroke-width: 0px; background-color: rgb(254,
          254, 254); text-decoration-thickness: initial;
          text-decoration-style: initial; text-decoration-color:
          initial; display: inline !important; float: none;">cpe wlan
          add 11/4/6<u>/2</u> admin-state up ssid 68A_uk encrypt-key
          redhound<span> <br>
          </span></span></b></p>
    <p><span style="color: rgb(0, 0, 0); font-family: CenturyGothicStd,
        &quot;Century Gothic&quot;, CenturyGothic; font-size: medium;
        font-style: normal; font-variant-ligatures: normal;
        font-variant-caps: normal; font-weight: 400; letter-spacing:
        normal; orphans: 2; text-align: start; text-indent: 0px;
        text-transform: none; white-space: normal; widows: 2;
        word-spacing: 0px; -webkit-text-stroke-width: 0px;
        background-color: rgb(254, 254, 254); text-decoration-thickness:
        initial; text-decoration-style: initial; text-decoration-color:
        initial; display: inline !important; float: none;"><span><span
            style="color: rgb(0, 0, 0); font-family: CenturyGothicStd,
            &quot;Century Gothic&quot;, CenturyGothic; font-size:
            medium; font-style: normal; font-variant-ligatures: normal;
            font-variant-caps: normal; font-weight: 400; letter-spacing:
            normal; orphans: 2; text-align: start; text-indent: 0px;
            text-transform: none; white-space: normal; widows: 2;
            word-spacing: 0px; -webkit-text-stroke-width: 0px;
            background-color: rgb(254, 254, 254);
            text-decoration-thickness: initial; text-decoration-style:
            initial; text-decoration-color: initial; display: inline
            !important; float: none;"><b>cpe wlan add 11/4/6<u>/6</u>
              admin-state up ssid 68A_uk encrypt-key redhound</b><span>
              <br>
            </span></span></span></span></p>
    <p><br>
    </p>
    <p>After this, resync the ONT, and make sure the new bridge gets a
      new IP address on the 10.5.0.0/24 range and check on the ONT
      webpage that the wifi is active. You can check with the bridge
      show command or on the webpage of the ONT, on
      config/interfaces/brouted and config/wireless(2.4 and 5)/basic.</p>
    <p>Ask the customer to connect to this wifi, and tell him to open
      this webpage:</p>
    <p><a class="moz-txt-link-freetext" href="https://www.ipfingerprints.com/">https://www.ipfingerprints.com/</a> <br>
    </p>
    <p>and check if its in the uk.<br>
    </p>

 