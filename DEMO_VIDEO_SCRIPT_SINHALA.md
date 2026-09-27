# 🌾 SmartAgro: University Presentation Demo Video Script & Guide
### (සම්පූර්ණ පද්ධතියේ Demonstration වීඩියෝ පිටපත සහ සිංහල කථන මාර්ගෝපදේශය)

---

## 📌 1. වීඩියෝවේ දළ විශ්ලේෂණය (Video Overview)
* **ව්‍යාපෘතිය (Project Name):** SmartAgro - Intelligent IoT Greenhouse & Farm Automation System
* **අරමුණ (Objective):** University Final Presentation / Viva එක සඳහා Web Portal, Flutter Mobile App සහ Physical IoT Hardware එකිනෙක සම්බන්ධ වී ක්‍රියාකරන ආකාරය පෙන්වන 100% ප්‍රායෝගික Demonstration වීඩියෝවක් සකස් කිරීම.
* **දළ කාලය (Total Duration):** විනාඩි 4 යි තත්පර 30 සිට 5 දක්වා (4:30 - 5:00 Mins)
* **භාවිතා වන තාක්ෂණයන් (Tech Stack):** 
  - **Hardware:** ESP32, DHT22 (Temp & Humidity), Capacitive Soil Moisture Sensor, 4-Channel 5V Relay Module, 16x2 I2C LCD, Actuators (Water Pump, Mist Pump, DC Fan, LED Grow Light).
  - **Backend & Web:** Laravel 11 REST API, MySQL, Filament v3 Admin Panel.
  - **Mobile:** Flutter (Dart), Firebase Cloud Messaging (FCM), Google Gemini AI API.

---

## 🎬 2. වීඩියෝව පටිගත කිරීමට පෙර සූදානම (Pre-Recording Checklist)
1. **Physical Setup:** 
   - මේසයක් මත ESP32 බෝඩ් එක, LCD Display එක, Relay මොඩියුලය සහ Actuators (කුඩා Water Pump එකක් වතුර වීදුරුවකට දමා, Fan, Light) පිළිවෙලකට සකස් කරගන්න.
   - පස් සහිත කුඩා බඳුනක් (Dry Soil) සහ වතුර වීදුරුවක් (Wet Soil simulation සඳහා) ළඟ තබා ගන්න.
2. **Software Setup:**
   - Laptop එකේ Laravel Backend එක `php artisan serve --host=0.0.0.0` හරහා run කර Filament Admin Panel (`http://localhost:8000/admin`) open කර තබන්න.
   - Smart Phone එකේ Flutter Mobile App එක run කර තබන්න.
   - ESP32 එක Power ON කර Wi-Fi සම්බන්ධ වී Server එකට Sync වන බව තහවුරු කරගන්න (LCD එකේ IP හෝ Readings පෙන්වයි).
3. **Recording Tools:**
   - Phone screen එක record කරගැනීමට Screen Recorder එකක් හෝ OBS Studio (Laptop Screen + Phone Screen + Camera Feed එකතු කර split-screen කිරීමට) භාවිතා කිරීම ඉතාම effective වේ.

---

## 📋 3. දර්ශනයෙන් දර්ශනයට සම්පූර්ණ පිටපත (Scene-by-Scene Script)

---

### 🎬 Scene 1: Introduction & System Overview (හැඳින්වීම)
* **කාලය (Duration):** `0:00 - 0:35` (තත්පර 35)
* **කැමරා කෝණය (Visual Action):**
  - මුලින්ම කැමරාව මුළු Setup එකම පෙනෙන සේ (Wide Shot) තබන්න: මේසය මත තිබෙන IoT Hardware Model එක, Laptop එකේ විවෘත කර ඇති Web Admin Panel එක සහ අතේ ඇති Mobile Phone එක එකම රාමුවක පෙනෙන්නට සලස්වන්න.
  - ඉන්පසු SmartAgro ලෝගෝව සහිත Title Slide එක හෝ Mobile App Splash Screen එක තත්පර 3ක් පෙන්වන්න.

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "ආයුබෝවන්! අද මම මගේ විශ්වවිද්‍යාල අවසාන ව්‍යාපෘතිය වන **'SmartAgro: Intelligent IoT Greenhouse & Farm Automation System'** එකෙහි පූර්ණ ක්‍රියාකාරීත්වය ප්‍රායෝගිකව ඉදිරිපත් කිරීමට සූදානම්. 
> 
> වර්තමාන කෘෂිකර්මාන්තයේ පවතින ජල නාස්තිය, ශ්‍රම හිඟය සහ දේශගුණික විපර්යාසයන්ට විසඳුමක් ලෙස, නවීන **IoT තාක්ෂණය, Cloud Backend, Web Admin Portal** සහ **AI-Powered Mobile Application** එකක් එකට ඒකාබද්ධ කරලා තමයි අපි මේ පද්ධතිය ගොඩනගා තිබෙන්නේ. 
> 
> මෙහිදී Hardware, Mobile App සහ Cloud පද්ධතිය එකිනෙක තත්‍ය කාලීනව (Real-time) සන්නිවේදනය වන ආකාරය දැන් අපි පියවරෙන් පියවර නිරීක්ෂණය කරමු."

---

### 🎬 Scene 2: Physical IoT Subsystem & Sensors (භෞතික උපකරණ පද්ධතිය)
* **කාලය (Duration):** `0:35 - 1:15` (තත්පර 40)
* **කැමරා කෝණය (Visual Action):**
  - කැමරාව Hardware Circuit එක වෙත Close-up කරන්න.
  - ඇඟිල්ලෙන් හෝ පෙන්වනයකින් එකින් එක පෙන්වන්න:
    1. ESP32 Microcontroller එක
    2. DHT22 Temperature & Humidity Sensor එක
    3. Soil Moisture Probe එක
    4. 16x2 I2C LCD Display එක (එහි අගයන් මාරුවන අයුරු)
    5. 4-Channel Relay Module එක (Pump, Mist, Fan, Grow Light සම්බන්ධ කර ඇති ආකාරය)
  - Soil probe එක වියළි පසෙන් ගෙන වතුර වීදුරුවකට දමන විට LCD එකේ Soil Moisture අගය වෙනස් වන ආකාරය පෙන්වන්න.

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "මුලින්ම අපගේ භෞතික IoT උපකරණ පද්ධතිය (Hardware Subsystem) දෙස බලමු. මෙහි ප්‍රධාන මොළය ලෙස ක්‍රියාකරන්නේ Wi-Fi පහසුකම සහිත **ESP32 Microcontroller** එකයි. 
> 
> පරිසරයේ උෂ්ණත්වය සහ ආර්ද්‍රතාවය නිවැරදිව මැනීමට **DHT22 සංවේදකයත්**, පසේ තෙතමනය මැනීමට **Soil Moisture සංවේදකයත්** මෙයට සම්බන්ධ කර තිබෙනවා. එම සියලුම දත්ත මෙම **16x2 I2C LCD තිරය** මත ක්ෂණිකව ප්‍රදර්ශනය වෙනවා. 
> 
> එමෙන්ම බෝගවලට අවශ්‍ය පරිසරය ස්වයංක්‍රීයව පාලනය කිරීම සඳහා **4-Channel Relay Module** එකක් මඟින් Drip Irrigation Pump එක, Mist Spray Pump එක, Exhaust Fan එක සහ Supplemental Grow Light එක පාලනය කිරීමට සම්බන්ධ කර තිබෙනවා. මෙම සංවේදක වලින් ලබාගන්නා දත්ත සෑම තත්පර 3කට වරක්ම HTTP REST API එක හරහා අපගේ Cloud Server එක වෙත Sync වීම සිදුවෙනවා."

---

### 🎬 Scene 3: Flutter Mobile App & Real-Time Sync (ජංගම යෙදුම සහ තත්‍ය කාලීන දත්ත)
* **කාලය (Duration):** `1:15 - 1:55` (තත්පර 40)
* **කැමරා කෝණය (Visual Action):**
  - Mobile Phone screen එක පැහැදිලිව පෙන්වන්න (හෝ Screen Record Overlay එකක් දමන්න).
  - Farmer account එකෙන් Mobile App එකට Login වන ආකාරය පෙන්වන්න.
  - Dashboard (Home Screen) එකේ Gauge Cards වල Temperature, Humidity, Soil Moisture අගයන් පෙන්වන්න.
  - කැමරාවෙන් Hardware LCD එකේ ඇති අගය සහ Mobile App එකේ Gauge එකේ ඇති අගය එක සමාන බව (Real-time Sync) එකම frame එකක පෙන්වන්න.

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "දැන් අපි ගොවියා භාවිතා කරන **Flutter Mobile Application** එක වෙත යොමුවෙමු. ගොවියා තම ගිණුමට Login වූ පසු, ඔහුගේ හරිතාගාරයේ පවතින සජීවී තොරතුරු Dashboard එකෙන් ඉතා පැහැදිලි visual gauges මඟින් දැකගත හැකියි.
> 
> ඔබ මෙහි දකින පරිදි, භෞතික Hardware LCD එකේ පෙන්වන උෂ්ණත්වය, ආර්ද්‍රතාවය සහ පසේ තෙතමන ප්‍රතිශතයම කිසිදු ප්‍රමාදයකින් තොරව Mobile App එකෙහි Real-time update වෙනවා. 
> 
> මෙහිදී ගොවියාට තමන් වගාකරන බෝගය (උදාහරණයක් ලෙස තක්කාලි හෝ පිපිඤ්ඤා) තෝරාගත හැකි අතර, ඒ අනුව බෝගයට අවශ්‍ය ප්‍රශස්ත තෙතමනය සහ උෂ්ණත්ව සීමාවන් පද්ධතිය විසින් ස්වයංක්‍රීයව හඳුනාගනු ලබනවා."

---

### 🎬 Scene 4: Dual Modes - Manual Remote Overrides & Auto Automation (ද්විත්ව පාලන ක්‍රමවේදය)
* **කාලය (Duration):** `1:55 - 2:45` (තත්පර 50) - **[මෙය වීඩියෝවේ වැදගත්ම කොටසයි (Highlight)]**
* **කැමරා කෝණය (Visual Action):**
  - **පළමු පියවර (Manual Mode):** 
    - Split Screen හෝ Side-by-side View: වම් පැත්තේ Phone Screen එක, දකුණු පැත්තේ Physical Relay / Pump / Fan.
    - App එකේ Mode එක `Manual` වලට දමන්න.
    - Mobile App එකේ "Drip Pump" toggle switch එක ON කරන්න -> එසැනින් Relay එක "ක්ලික්" හඬින් ON වී Relay LED එක දැල්වී Water pump එක වැඩකරන්න පටන් ගන්නා අයුරු පෙන්වන්න.
    - App එකෙන් "Exhaust Fan" හෝ "Grow Light" ON කරන්න -> Fan එක කැරකෙන්නට පටන් ගන්නා අයුරු සහ Light එක දැල්වෙන අයුරු පෙන්වන්න.
    - නැවත App එකෙන් Switch OFF කරන විට උපකරණ ක්‍රියාවිරහිත වන අයුරු පෙන්වන්න.
  - **දෙවන පියවර (Auto Mode):**
    - App එක `Auto` Mode එකට මාරු කරන්න.
    - Soil probe එක වතුරෙන් පිටතට ගන්න (Dry Soil Simulation). තෙතමනය අඩු වූ සැනින් ESP32 logic එක මඟින් Drip Pump Relay එක ස්වයංක්‍රීයව ON වන ආකාරය පෙන්වන්න.
    - Probe එක නැවත වතුරට දැමූ පසු තෙතමනය සම්පූර්ණ වී Pump එක ස්වයංක්‍රීයව OFF වන අයුරු පෙන්වන්න.

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "SmartAgro පද්ධතිය සතුව **Auto** සහ **Manual** ලෙස ප්‍රධාන පාලන ක්‍රමවේද දෙකක් පවතිනවා. 
> 
> මුලින්ම මම පද්ධතිය **Manual Mode** එකට මාරු කර පෙන්වන්නම්. බලන්න, මම මගේ ජංගම දුරකථනයෙන් Drip Pump එක ON කල සැනින්, Cloud හරහා උපදෙස් ලැබී මෙහි භෞතික Relay එක ක්‍රියාත්මක වී ජල පොම්පය වැඩ කිරීමට පටන් ගන්නවා. ඒ වගේම මම Fan එක හෝ Grow Light එක Switch ON කරන විට කිසිදු ප්‍රමාදයකින් තොරව එම උපකරණ දුරස්ථව පාලනය වෙනවා.
> 
> දැන් අපි මෙය **Auto Mode** එකට දමමු. මෙහිදී ස්වයංක්‍රීය ඇල්ගොරිතමය මඟින් තීරණ ගනු ලබනවා. මම පසේ සංවේදකය වියළි පරිසරයකට ගත් විට, පසේ තෙතමනය නියමිත සීමාවට වඩා අඩු වූ බව පද්ධතිය හඳුනාගෙන, කිසිදු මිනිස් මැදිහත්වීමකින් තොරව ස්වයංක්‍රීයවම Drip Pump එක ක්‍රියාත්මක කර පැළයට ජලය සපයනවා. නැවත පස ප්‍රමාණවත් ලෙස තෙත් වූ පසු පොම්පය ස්වයංක්‍රීයව ක්‍රියාවිරහිත වෙනවා. මේ හරහා 100% ක් ජල නාස්තිය අවම කරගැනීමට හැකියි."

---

### 🎬 Scene 5: AI Agronomist & Smart Features (කෘත්‍රිම බුද්ධිය සහ විශේෂාංග)
* **කාලය (Duration):** `2:45 - 3:20` (තත්පර 35)
* **කැමරා කෝණය (Visual Action):**
  - Mobile App එකේ "AI Agronomist" Chat Screen එකට යන්න.
  - ගොවියා ප්‍රශ්නයක් අසන ආකාරය (උදා: *"මගේ තක්කාලි කොළ කහ පාට වෙලා, මොකක්ද කරන්න ඕන?"* හෝ *"What is the best fertilizer for flowering stage?"*).
  - Google Gemini AI එක මඟින් ක්ෂණිකව විද්‍යාත්මක, කෘෂිකාර්මික උපදෙස් සහ පොහොර යෙදීම් පිළිතුරක් ලෙස ලැබෙන ආකාරය පෙන්වන්න.
  - Irrigation Schedule Screen එක පෙන්වන්න (දවසේ වේලාව අනුව වතුර දමන ටයිමර් සැකසීම).

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "සාමාන්‍ය ස්වයංක්‍රීය පද්ධතියකට එහා ගිය සුවිශේෂී පහසුකමක් ලෙස, අපි මෙහි **Google Gemini AI තාක්ෂණයෙන් බලගැන්වුණු 'AI Agronomist Assistant'** කෙනෙකු ඇතුළත් කර තිබෙනවා. 
> 
> ගොවියාට තම වගාවේ ඇතිවන රෝග ලක්ෂණ හෝ පොහොර භාවිතය පිළිබඳ ඕනෑම ගැටලුවක් මෙහිදී විමසිය හැකි අතර, AI සහායකයා විසින් ක්ෂණිකව නිවැරදි කෘෂිකාර්මික උපදෙස් ලබාදෙනවා. 
> 
> මීට අමතරව ගොවියාට පහසුවෙන්ම නිශ්චිත වේලාවන් වලදී ජලය සැපයීමට **Irrigation Schedules** සකස් කිරීමේ හැකියාවද මෙම ඇප් එක සතුයි."

---

### 🎬 Scene 6: Laravel Filament Web Admin Portal & Billing Slip Audit (පරිපාලන වෙබ් අඩවිය සහ ගෙවීම් පරීක්ෂාව)
* **කාලය (Duration):** `3:20 - 4:05` (තත්පර 45)
* **කැමරා කෝණය (Visual Action):**
  - Laptop එකේ Screen Record එක පෙන්වන්න (Filament v3 Admin Panel).
  - Dashboard එකේ Microclimate Analytics සහ Active Users සංඛ්‍යාලේඛන පෙන්වන්න.
  - **Payment Verification Workflow:**
    1. Phone එකෙන් Farmer කෙනෙක් Subscription එක සඳහා Bank Slip එකක් Upload කරන ආකාරය (Submit Payment Screen) තත්පර 5කින් පෙන්වන්න.
    2. Admin Panel එකේ "Payments" tab එක Refresh කරන විට එම Slip එක Pending ලෙස දිස්වන අයුරු පෙන්වන්න.
    3. Admin විසින් Slip Image එක View කර, "Approve" බොත්තම ක්ලික් කරන අයුරු පෙන්වන්න.
    4. එසැනින් ගොවියාගේ ගිණුම මාසික සේවාව සඳහා Approved වන ආකාරය පෙන්වන්න.

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "දැන් අපි පද්ධතියේ පරිපාලන කටයුතු සිදුකරන **Filament v3 මඟින් නිර්මාණය කරන ලද Web Admin Portal** එක වෙත අවධානය යොමු කරමු.
> 
> පරිපාලකවරයාට මෙහි ඇති Analytics Dashboard එක හරහා සියලුම ගොවීන්ගේ හරිතාගාර වල උෂ්ණත්වය, ආර්ද්‍රතාවය සහ පද්ධති තත්ත්වයන් එක් තැනක සිට අධීක්ෂණය කළ හැකියි. 
> 
> එමෙන්ම අපගේ Subscription Business Model එකට අනුව, ගොවියා ජංගම යෙදුම හරහා උඩුගත කරන බැංකු තැන්පතු රිසිට්පත (Bank Slip), පරිපාලකවරයාට මෙලෙස Admin Panel එකෙන් පරීක්ෂා කර තහවුරු කළ හැකියි. පරිපාලක 'Approve' කළ සැනින්, ගොවියාගේ ගිණුමේ සේවාවන් අඛණ්ඩව ක්‍රියාත්මක වෙනවා. පරිපාලක සහ ගොවියා අතර සජීවීව පණිවිඩ හුවමාරු කරගැනීමේ Support Chat පහසුකමද මෙහි අන්තර්ගතයි."

---

### 🎬 Scene 7: Push Notifications & Safety Alerts (හදිසි දැනුම්දීම්)
* **කාලය (Duration):** `4:05 - 4:30` (තත්පර 25)
* **කැමරා කෝණය (Visual Action):**
  - Phone Screen එක පෙන්වන්න.
  - පසේ තෙතමනය අනතුරුදායක මට්ටමකට පහත වැටුණු විට හෝ උෂ්ණත්වය අධික වූ විට Firebase Cloud Messaging (FCM) හරහා Phone Notification Bar එකට එන Alert Popup එක පෙන්වන්න: *"⚠️ SmartAgro Alert: Critical Soil Moisture Drop Detected!"*

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "ගොවියා යෙදුම භාවිතා නොකරන අවස්ථාවක පවා, පරිසරයේ උෂ්ණත්වය අනතුරුදායක ලෙස ඉහළ ගියහොත් හෝ පස අධික ලෙස වියළී ගියහොත්, **Firebase Cloud Messaging** තාක්ෂණය ඔස්සේ ගොවියාගේ ජංගම දුරකථනයට ක්ෂණික Push Notification එකක් ලැබෙනවා. එමඟින් සිදුවිය හැකි ඕනෑම වගා හානියක් කල්තියා වළක්වා ගැනීමට ගොවියාට හැකි වෙනවා."

---

### 🎬 Scene 8: Conclusion & Wrap-up (සමාලෝචනය සහ අවසානය)
* **කාලය (Duration):** `4:30 - 4:55` (තත්පර 25)
* **කැමරා කෝණය (Visual Action):**
  - නැවතත් කැමරාව මුළු පද්ධතියම (Hardware + Laptop + Phone) පෙනෙන සේ Wide Shot එකකට ගන්න.
  - අවසාන Thank You slide එක සහ University / Group Details තිරයේ පෙන්වන්න.

> 🎙️ **සිංහල කථනය (Speech / Voiceover):**
> 
> "සමස්තයක් ලෙස ගත් කල, **SmartAgro** යනු ගොවියාගේ ශ්‍රමය, කාලය සහ ජලය උපරිමයෙන් ඉතිරි කරමින්, අස්වැන්නේ ගුණාත්මකභාවය ඉහළ නැංවීමට නවීන තාක්ෂණය සාර්ථකව යොදාගත් ප්‍රායෝගික විසඳුමක්. 
> 
> IoT, Cloud Computing සහ Artificial Intelligence ඒකාබද්ධ කරමින් නිර්මාණය කළ අපගේ SmartAgro ව්‍යාපෘතියේ Demonstration වීඩියෝව නැරඹූ ඔබ සැමට ස්තූතියි!"

---

## 💡 4. වීඩියෝව සාර්ථක කරගැනීමට වටිනා උපදෙස් (Pro Presentation Tips)

1. **Split-Screen Layout (OBS Studio නිර්දේශය):**
   - වීඩියෝව Edit කරන විට හෝ Record කරන විට Screen එක කොටස් දෙකකට හෝ තුනකට බෙදන්න:
     - **කොටස 1 (40%):** Physical Hardware (Relays, Sensors, Pumps) පෙනෙන කැමරා Feed එක.
     - **කොටස 2 (30%):** Mobile Phone Screen Record එක (Switch එක ඔබන ආකාරය).
     - **කොටස 3 (30%):** Web Admin Panel එක හෝ Sensor Live Graph එක.
   - එවිට App එකේ Switch එකක් ඔබන විටම Relay එක වැඩකරන අයුරු එකම මොහොතේ පරීක්ෂකවරුන්ට (Examiners) දැකගත හැකි නිසා ඉහළම ලකුණු ප්‍රමාණයක් ලබාගත හැකියි.

2. **ශබ්දය (Audio Quality):**
   - කථනය පටිගත කිරීමේදී නිහඬ පරිසරයකදී Phone එකේ Earphone Mic එක හෝ Collar Mic එකක් භාවිතා කරන්න. Relay එක switch වන "ටක්" හඬ පසුබිමින් ඇසෙන්නට හැරීමෙන් පද්ධතිය සැබවින්ම physical ලෙස ක්‍රියාකරන බව වඩාත් තහවුරු වේ.

3. **Subtitles (උපසිරැසි):**
   - ඔබ කතා කරන්නේ සිංහලෙන් වුවද, වීඩියෝවේ පහළින් ප්‍රධාන තාක්ෂණික පද (e.g., *ESP32 Sensor Telemetry Ingestion*, *Manual Actuator Remote Override via REST API*, *Automated Threshold Microclimate Control*) ඉංග්‍රීසි Subtitles / Captions ලෙස ඇතුළත් කරන්න. එය University Academic standard එකට ඉතා වටිනවා.
