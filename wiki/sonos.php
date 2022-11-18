<div class="main">
    <h1>Disabling the WiFi Link on a Sonos Music Player</h1>

<p><span class="plugin_picture align-right">

<img src="//static.bsteiner.info/media/cms_page_media/15/sonosnet_1.jpg" alt="SonosNet" title="The SonosNet wireless mesh links the players">

</span></p>

<p>All Sonos players attempt to establish a peer-to-peer wireless mesh network known as SonosNet as soon as they are powered up. While this is convenient, there are several situations in which turning off this WiFi connection makes sense:</p>

<ul>
	<li>You own a single player that you connected directly to your home router with an Ethernet cable. You don't need the built-in SonosNet, so why not deactivate it to reduce power consumption and electromagnetic radiations.</li>
	<li>You live in a neighborhood where many WiFi networks are already crowding up the spectrum. You've hard-wired all your players to make sure the music streams smoothly, free of glitches and interruptions. You don't want to create new sources of WiFi interference, making an already bad situation even worse.</li>
	<li>SonosNet relies on the spanning tree protocol (aka STP) to function properly, so if your other network equipment doesn't support this functionality your entire network will be overloaded by broadcast storms and frequently crash. Instead of upgrading your network it is much easier and cheaper to eliminate the source of the problem.</li>
	<li>You're worried about WiFi-Jacking. Why leave a backdoor in your network that can't be strongly secured?</li>
</ul>

<p>It is possible to switch on or off the wireless adapter of each Sonos player individually. Here's how in 3 simple steps.</p>

<h2>Step 1: Finding the IP address of the device</h2>

<p>From the Sonos controller, click on the "about my sonos system" menu. You should see something like this:</p>

<pre>PLAY:5: Bedroom
Serial Number: 00-0E-58-2D-B0-C3:3 
Version: 4.2 (build 24071060) 
Hardware Version: 1.16.4.1-1 
IP Address: 192.168.1.27 
OTP: 1.1.1(1-16-4-zp5s-0.5)</pre>

<p>In the example above, the address is 192.168.1.27. We'll refer to it as &lt;sonos_ip&gt; in the rest of this article.</p>

<p>If you feel more technically inclined, you can also retrieve the IP address from your DHCP server. Sonos registers its players under the "SonosZP" client ID.</p>

<h2>Step 2: Checking the status of the Wifi link</h2>

<p>Sonos provides a little known  on the port 1400 of their players that you can access from any web browser at the following URL:</p>

<pre>http://&lt;sonos_ip&gt;:1400/status/ifconfig</pre>

<p>You should see something like this:</p>



</span></p>

<p>The entrie labeled 'eth0' and 'eth1' correspond to the 2 wired ports. The 'lo' and 'br0' interfaces are virtual networking devices used internally by the Linux kernel. The entry we're interested in is labeled 'ath0', which stands for Atheros device 0. Atheros is the manufacturer of the embedded WiFi chip.</p>

<h2>Step 3: Disabling the link</h2>

<p>To disable the WiFi link start by issuing the following HTTP request:</p>

<pre>http://&lt;sonos_ip&gt;:1400/wifictrl?wifi=off</pre>

<p>You should get the following answer:</p>

<pre>wifictrl request succeeded HTTP 200 OK</pre>

<p>You can also check that the link has indeed been disabled by going back to the status page. The 'ath0' entry should not be present anymore. The setting is not persistent, so if you happen to be unable to connect to your player after disabling the WiFi you can undo the change by power cycling the player.</p>

<p>If you want to disable the WiFi link for good, simply issue the following http request:</p>

<pre>http://&lt;sonos_ip&gt;:1400/wifictrl?wifi=persist-off</pre>

<p>The change will now be preserved even after an upgrade. If you ever need to connect the player wirelessly in the future you can turn the WiFi back on as follow:</p>

<pre>http://&lt;sonos_ip&gt;:1400/wifictrl?wifi=on</pre>

<h2>Impact on power consumption</h2>

<p>I measured the power consumption of several players with a wattmeter which is accurate to +/- 0.5 watt. Turning off the WiFi link reduces the power consumption of the players by about 2 Watts.  Here are the results measured when the players are idle:</p>

<table>
	<tbody>
		<tr>
			<th>Player</th>
			<th>WiFi ON</th>
			<th>WiFi OFF</th>
		</tr>
		<tr>
			<td>Play:5</td>
			<td>6.5W</td>
			<td>4.5W</td>
		</tr>
		<tr>
			<td>Connect</td>
			<td>4W</td>
			<td>2W</td>
		</tr>
		<tr>
			<td>Connect:AMP</td>
			<td>6.5W</td>
			<td>4.5W</td>
		</tr>
	</tbody>
</table>

 
</div>

