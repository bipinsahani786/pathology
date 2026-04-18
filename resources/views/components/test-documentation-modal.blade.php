<!-- Test Documentation Modal -->
<div class="modal fade" id="testDocumentationModal" tabindex="-1" aria-labelledby="testDocumentationModalLabel" aria-hidden="true" x-data="{ lang: localStorage.getItem('doc_lang') || 'en' }" x-init="$watch('lang', value => localStorage.setItem('doc_lang', value))">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-primary bg-gradient p-4 align-items-center border-0">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white p-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="feather-book-open fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="modal-title fw-bolder mb-1 text-white text-shadow-sm" id="testDocumentationModalLabel">
                                <span x-show="lang === 'en'">Laboratory Management Handbook</span>
                                <span x-show="lang === 'hi'" style="display: none;">लैबोरेटरी मैनेजमेंट हैंडबुक</span>
                            </h4>
                            <p class="mb-0 text-white fs-12 opacity-85">
                                <span x-show="lang === 'en'">Comprehensive 8-Chapter Protocol & Guide (v2.6.5)</span>
                                <span x-show="lang === 'hi'" style="display: none;">विस्तृत 8-अध्याय प्रोटोकॉल और गाइड (v2.6.5)</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Language Switcher - High Contrast Design -->
                    <div class="bg-black bg-opacity-10 p-1 rounded-pill d-flex gap-1 border border-white border-opacity-25 shadow-sm">
                        <button type="button" @click="lang = 'en'" :class="lang === 'en' ? 'bg-white text-primary shadow-sm' : 'text-white'" class="btn btn-sm rounded-pill px-3 fw-bold transition-all border-0 shadow-none">EN</button>
                        <button type="button" @click="lang = 'hi'" :class="lang === 'hi' ? 'bg-white text-primary shadow-sm' : 'text-white'" class="btn btn-sm rounded-pill px-3 fw-bold transition-all border-0 shadow-none">HI (हिंदी)</button>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none ms-3 opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0 bg-white">
                <div class="row g-0">
                    <!-- Sidebar Navigation -->
                    <div class="col-md-3 bg-light border-end d-none d-md-block p-4"
                        style="position: sticky; top: 0; align-self: flex-start; height: 100%;">
                        <h6 class="fw-bold text-uppercase text-dark fs-10 tracking-widest mb-3 opacity-75">Chapters / अध्याय</h6>
                        <ul class="nav flex-column nav-pills gap-1 documentation-nav">
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2 active" href="#ch1"><i class="feather-grid me-2"></i> <span x-show="lang === 'en'">1. Architecture</span><span x-show="lang === 'hi'" style="display: none;">1. आर्किटेक्चर</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch2"><i class="feather-tag me-2"></i> <span x-show="lang === 'en'">2. Naming & Codes</span><span x-show="lang === 'hi'" style="display: none;">2. नामकरण और कोड</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch3"><i class="feather-layers me-2"></i> <span x-show="lang === 'en'">3. Departments</span><span x-show="lang === 'hi'" style="display: none;">3. विभाग और कैटेगरी</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch4"><i class="feather-dollar-sign me-2"></i> <span x-show="lang === 'en'">4. Financials</span><span x-show="lang === 'hi'" style="display: none;">4. मूल्य निर्धारण</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch5"><i class="feather-truck me-2"></i> <span x-show="lang === 'en'">5. Logistics & TAT</span><span x-show="lang === 'hi'" style="display: none;">5. सैंपल और समय</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch6"><i class="feather-zap me-2"></i> <span x-show="lang === 'en'">6. Parameters</span><span x-show="lang === 'hi'" style="display: none;">6. टेस्ट मापदंड</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch7"><i class="feather-activity me-2"></i> <span x-show="lang === 'en'">7. Range Wizard</span><span x-show="lang === 'hi'" style="display: none;">7. सामान्य सीमा</span></a></li>
                            <li class="nav-item"><a class="nav-link text-dark fw-bold rounded-3 px-3 py-2" href="#ch8"><i class="feather-message-square me-2"></i> <span x-show="lang === 'en'">8. Interpretations</span><span x-show="lang === 'hi'" style="display: none;">8. चिकित्सा व्याख्या</span></a></li>
                        </ul>
                    </div>

                    <!-- Content Area -->
                    <div class="col-md-9 p-4 p-md-5 bg-white">
                        <div class="documentation-content text-dark">

                            <!-- CHAPTER 1: ARCHITECTURE -->
                            <div id="ch1" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 1: System Architecture</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 1: सिस्टम आर्किटेक्चर</span>
                                </h2>
                                <p class="fs-14 lh-lg mb-4">
                                    <span x-show="lang === 'en'">Our pathology software uses a <strong>Centralized Master-Local Child</strong> pattern. This architecture is designed to provide consistency while allowing lab-level flexibility.</span>
                                    <span x-show="lang === 'hi'" style="display: none;">हमारा पैथोलॉजी सॉफ्टवेयर एक <strong>Centralized Master-Local Child</strong> पैटर्न का उपयोग करता है। यह आर्किटेक्चर लैब-स्तर की लचीलापन देते हुए डेटा की सटीकता सुनिश्चित करता है।</span>
                                </p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-soft-primary rounded-4">
                                            <div class="card-body p-4">
                                                <h6 class="fw-bold text-primary mb-2">Master Library</h6>
                                                <p class="fs-12 mb-0">Managed by Superadmins. This is the "Gold Standard" library containing medically validated tests. Labs can import these to save 90% of setup time.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-soft-info rounded-4">
                                            <div class="card-body p-4">
                                                <h6 class="fw-bold text-info mb-2">Lab-Specific Catalog</h6>
                                                <p class="fs-12 mb-0">Managed by individual lab owners. They can modify prices or even create entirely unique tests that do not exist in the master library.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CHAPTER 2: NAMING & CODES -->
                            <div id="ch2" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 2: Naming & Identification</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 2: नामकरण और पहचान</span>
                                </h2>
                                <div class="bg-light p-4 rounded-4 mb-4">
                                    <h6 class="fw-bold text-dark mb-3">
                                        <span x-show="lang === 'en'">Why "Test Code" is Critical?</span>
                                        <span x-show="lang === 'hi'" style="display: none;">"टेस्ट कोड" क्यों महत्वपूर्ण है?</span>
                                    </h6>
                                    <p class="fs-13 lh-base">
                                        <span x-show="lang === 'en'">The Test Code (e.g., <code>CBC-100</code>) is much more than just a label. It acts as the backbone for:</span>
                                        <span x-show="lang === 'hi'" style="display: none;">टेस्ट कोड (उदाहरण के लिए, <code>CBC-100</code>) सिर्फ एक लेबल से कहीं अधिक है। यह इनके लिए रीढ़ की हड्डी के रूप में कार्य करता है:</span>
                                    </p>
                                    <ul class="fs-13 ps-3">
                                        <li><strong>Barcode Scanning:</strong> When samples are processed by machines, the barcode utilizes this unique code to identify the test.</li>
                                        <li><strong>Quick Search:</strong> Registration staff can type "CBC" instead of the full test name to book patients instantly.</li>
                                        <li><strong>LIS Integration:</strong> It allows synchronization with analyzer machines.</li>
                                    </ul>
                                </div>
                                <div class="p-3 border rounded-4 border-primary border-opacity-25 bg-soft-primary">
                                    <h6 class="fw-bold mb-1">Standardized Naming</h6>
                                    <p class="fs-12 mb-0">Always use the full medical name for the 'Test Name' (e.g. Complete Blood Count) and a concise version for 'Short Name' (CBC). The report will utilize the full name for professional appearance.</p>
                                </div>
                            </div>

                            <!-- CHAPTER 3: DEPARTMENTS -->
                            <div id="ch3" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 3: Departmental Governance</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 3: विभाग और व्यवस्था</span>
                                </h2>
                                <p class="fs-14 lh-lg mb-4">
                                    <span x-show="lang === 'en'">Every test <strong>MUST</strong> be mapped to a department. This is not just for organization; it controls the 'Worklist Routing'.</span>
                                    <span x-show="lang === 'hi'" style="display: none;">प्रत्येक टेस्ट को एक विभाग (Department) से मैप किया जाना <strong>अनिवार्य</strong> है। यह केवल संगठन के लिए नहीं है; यह 'वर्कलिस्ट रूटिंग' को नियंत्रित करता है।</span>
                                </p>
                                <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
                                    <table class="table table-hover mb-0 fs-13">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th class="py-3 px-4 border-0">Department</th>
                                                <th class="py-3 px-4 border-0">Clinical Impact</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold px-4 py-3">Hematology / Pathology</td>
                                                <td class="px-4 py-3 text-muted">Tests involving blood cells (CBC, ESR). Printed under the Pathology header on final reports.</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold px-4 py-3">Biochemistry</td>
                                                <td class="px-4 py-3 text-muted">Chemical analysis (Lipid, LFT). These often require specialized analyzer machines.</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold px-4 py-3">Microbiology</td>
                                                <td class="px-4 py-3 text-muted">Culture and sensitivity studies. These have a longer TAT (Turn Around Time).</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- CHAPTER 4: FINANCIALS -->
                            <div id="ch4" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 4: Pricing & Financial Sync</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 4: मूल्य और वित्तीय प्रबंधन</span>
                                </h2>
                                <div class="space-y-4">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="p-4 rounded-4 border-start border-4 border-success bg-soft-success">
                                                <h6 class="fw-bold text-success mb-2">Suggested MRP vs Actual Price</h6>
                                                <p class="fs-13 mb-0 text-dark">
                                                    <span x-show="lang === 'en'">Superadmins set a **Suggested MRP** in the Master settings. This act as a guide. When a lab imports the test, they can either keep this price or change it according to their local competition.</span>
                                                    <span x-show="lang === 'hi'" style="display: none;">सुपरएडमिन मास्टर सेटिंग्स में एक **सुझाया गया MRP** सेट करते हैं। जब कोई लैब टेस्ट इम्पोर्ट करती है, तो वे या तो इस मूल्य को रख सकते हैं या अपनी स्थानीय प्रतिस्पर्धा के अनुसार इसे बदल सकते हैं।</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pt-3">
                                            <h6 class="fw-bold mb-1">B2B Rate Card</h6>
                                            <p class="fs-12 text-muted">This is the 'Referral Price' given to partner hospitals or clinics. It ensures that when you collaborate with external labs, your margins remain protected.</p>
                                        </div>
                                        <div class="col-md-6 pt-3">
                                            <h6 class="fw-bold mb-1">Tax Calculation</h6>
                                            <p class="fs-12 text-muted">Ensure you specify if the price is Inclusive or Exclusive of TAX in your global lab settings to avoid billing discrepancies.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CHAPTER 5: LOGISTICS & TAT -->
                            <div id="ch5" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 5: Sample Logistics & TAT</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 5: सैंपल और समय (TAT) प्रबंधन</span>
                                </h2>
                                <p class="fs-14 lh-lg mb-4">
                                    <span x-show="lang === 'en'">Logistics fields ensure that the lab staff knows exactly what to collect from the patient.</span>
                                    <span x-show="lang === 'hi'" style="display: none;">लॉजिस्टिक्स फील्ड्स यह सुनिश्चित करते हैं कि लैब स्टाफ को पता हो कि पेशेंट से क्या और कैसे कलेक्ट करना है।</span>
                                </p>
                                <div class="bg-light p-4 rounded-4 shadow-sm border">
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark mb-1"><i class="feather-box text-primary me-2"></i>Sample Type</h6>
                                        <p class="fs-12 text-muted">Specify the sample (Whole Blood, EDTA Plasma, Serum, Spot Urine, etc). This is printed on the barcode sticker to help the processing department.</p>
                                    </div>
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark mb-1"><i class="feather-activity text-info me-2"></i>Methodology</h6>
                                        <p class="fs-12 text-muted">The clinical method used (e.g. <strong>ECLIA, ELISA, Nephelometry</strong>). This information is printed at the bottom of each test section on results for medical validity.</p>
                                    </div>
                                    <div class="mb-0">
                                        <h6 class="fw-bold text-dark mb-1"><i class="feather-clock text-warning me-2"></i>TAT Hours (Turn Around Time)</h6>
                                        <p class="fs-12 text-muted">Total number of hours expected to generate a report. The system calculates the 'Due Time' from the registration time. If the report is late, it will highlight red on the dashboard.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- CHAPTER 6: PARAMETERS -->
                            <div id="ch6" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 6: Parameter Science</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 6: टेस्ट मापदंड (Parameter Science)</span>
                                </h2>
                                <div class="p-4 border rounded-4 border-primary border-opacity-10 bg-soft-primary mb-4">
                                    <h6 class="fw-bold text-primary mb-3">Input Types: Which one to choose?</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 border-end border-white">
                                            <p class="fs-12 mb-1"><strong>Numerical:</strong> Standard numbers. Use for any test that has a measurable value and high/low markers.</p>
                                            <p class="fs-12 mb-0"><strong>Text / Qualitative:</strong> Free text entry. Best for biopsy notes or simple qualitative results.</p>
                                        </div>
                                        <div class="col-md-6 ps-3">
                                            <p class="fs-12 mb-1"><strong>Dropdown:</strong> Choose from fixed options (e.g. Positive, Negative, Reactive). Best for standardization.</p>
                                            <p class="fs-12 mb-0"><strong>Formula:</strong> Automatically calculated using other parameters. Does not allow manual entry.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning border-0 rounded-4 p-4 shadow-sm">
                                    <h6 class="fw-bold mb-1 tracking-tight">Understanding "Short Codes"</h6>
                                    <p class="fs-11 mb-2">Short Codes (e.g. <code>HB</code>, <code>RBC</code>) are internal identifiers. They are invisible to patients but critical for our <strong>Formula Engine</strong>.</p>
                                    <p class="fs-11 mb-0 border-top pt-2 opacity-75">Tip: Keep Short Codes in CAPITAL letters without special characters for better accuracy in calculations.</p>
                                </div>
                            </div>

                            <!-- CHAPTER 7: RANGE WIZARD -->
                            <div id="ch7" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 7: The Range Wizard Engine</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 7: सामान्य सीमा (Range) इंजन</span>
                                </h2>
                                <p class="fs-14 lh-lg mb-4">
                                    <span x-show="lang === 'en'">Reference ranges define the "Normal Condition". Our system uses a multi-layered selection logic.</span>
                                    <span x-show="lang === 'hi'" style="display: none;">संदर्भ सीमाएँ (Reference Ranges) "सामान्य स्थिति" को परिभाषित करती हैं। हमारा सिस्टम एक बहु-स्तरीय लॉजिक का उपयोग करता है।</span>
                                </p>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-4 h-100 bg-light">
                                            <h6 class="fw-bold mb-2 fs-13"><i class="feather-user me-2 text-primary"></i>Demographic Filtering</h6>
                                            <p class="fs-12 mb-0">If you add a range for 'Male' only, the system will hide this range row if the registered patient is 'Female'. This prevents incorrect interpretations.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-4 h-100 bg-light">
                                            <h6 class="fw-bold mb-2 fs-13"><i class="feather-calendar me-2 text-info"></i>Age Sensitivity</h6>
                                            <p class="fs-12 mb-0">You can define ranges for 0-30 <strong>Days</strong> for infants and 18-99 <strong>Years</strong> for adults. The system converts DOB to days instantly to pick the winner range.</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded-4 bg-soft-success border-success border-opacity-25 h-100">
                                            <h6 class="fw-bold mb-2 fs-13 text-success">Min/Max vs. Display Range</h6>
                                            <p class="fs-12 mb-0">
                                                <strong>Min-Max:</strong> Used for logic and calculations. (e.g. 10.0 to 20.0). Results outside this will turn <strong>BOLD RED</strong>.<br>
                                                <strong>Display String:</strong> Used for printing. You can write <code>Negative</code> or <code>< 6.5 %</code>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CHAPTER 8: INTERPRETATIONS -->
                            <div id="ch8" class="mb-5 border-bottom pb-5">
                                <h2 class="fw-bolder text-primary mb-4">
                                    <span x-show="lang === 'en'">Chapter 8: Clinical Interpretations</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अध्याय 8: चिकित्सकीय व्याख्या और नोट्स</span>
                                </h2>
                                <div class="p-4 border rounded-4 shadow-sm border-start border-4 border-primary bg-white">
                                    <p class="fs-14 mb-4">
                                        <span x-show="lang === 'en'">This is the detailed medical text provided at the end of every test section. It transforms a simple result into a medical consultation.</span>
                                        <span x-show="lang === 'hi'" style="display: none;">यह प्रत्येक टेस्ट के अंत में दिया जाने वाला विस्तृत मेडिकल टेक्स्ट है। यह एक साधारण रिजल्ट को मेडिकल परामर्श में बदल देता है।</span>
                                    </p>
                                    <h6 class="fw-bold fs-13 mb-2 text-dark">Best Practices for Interpretation:</h6>
                                    <ul class="fs-12 text-muted ps-3 mb-0">
                                        <li class="mb-2"><strong>Formatting:</strong> Use our rich-text editor to add **Bold** headers for Clinical Significance.</li>
                                        <li class="mb-2"><strong>Standardization:</strong> Import these from Master to ensure all your reports use the latest medical standards.</li>
                                        <li><strong>Lab Localization:</strong> Lab admins can add their own "Disclaimer" or local contact info at the bottom of interpretations.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- FAQ SECTION (REFINED) -->
                            <div id="faq" class="mb-5 pt-4 border-top">
                                <h3 class="fw-bolder text-dark mb-4 d-flex align-items-center gap-2">
                                    <span class="bg-dark text-white w-8 h-8 rounded-circle d-flex align-items-center justify-content-center fs-12 shadow-sm">?</span>
                                    <span x-show="lang === 'en'">Frequently Asked Questions</span>
                                    <span x-show="lang === 'hi'" style="display: none;">अक्सर पूछे जाने वाले सवाल</span>
                                </h3>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="p-4 rounded-4 border bg-white shadow-sm hover-shadow transition-all">
                                            <h6 class="fw-bold text-primary mb-2">
                                                <span x-show="lang === 'en'">How do I copy a test from Master to my Lab?</span>
                                                <span x-show="lang === 'hi'" style="display: none;">मैं मास्टर से अपनी लैब में टेस्ट कैसे कॉपी करूँ?</span>
                                            </h6>
                                            <p class="fs-13 text-dark mb-0 opacity-75">
                                                <span x-show="lang === 'en'">Go to your <strong>Lab Test Library</strong>, search for the test in the "Master Library" tab, and click the blue "Import" button. All parameters and ranges are copied instantly.</span>
                                                <span x-show="lang === 'hi'" style="display: none;">अपनी <strong>Lab Test Library</strong> में जाएं, "Master Library" टैब में टेस्ट खोजें, और नीले "Import" बटन पर क्लिक करें।</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-4 rounded-4 border bg-white shadow-sm hover-shadow transition-all border-warning border-opacity-25">
                                            <h6 class="fw-bold text-warning mb-2">
                                                <span x-show="lang === 'en'">My Formula is not working! What's wrong?</span>
                                                <span x-show="lang === 'hi'" style="display: none;">मेरा फार्मूला काम नहीं कर रहा है! क्या गलत है?</span>
                                            </h6>
                                            <p class="fs-13 text-dark mb-0 opacity-75">
                                                <span x-show="lang === 'en'">Check if you've wrapped codes in curly braces <code>{ }</code>. Also, ensure the <strong>Short Codes</strong> exactly match (case-sensitive) between the formula and the parameter settings.</span>
                                                <span x-show="lang === 'hi'" style="display: none;">जांचें कि क्या आपने कोड को कर्ली ब्रैकेट <code>{ }</code> में रखा है। साथ ही सुनिश्चित करें कि <strong>Short Codes</strong> बिल्कुल मैच करते हों।</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light p-3 border-top d-flex justify-content-between">
                <p class="fs-12 text-dark font-medium mb-0">
                    <i class="feather-shield text-success me-2"></i>
                    <span x-show="lang === 'en'">Secure Laboratory Management Protocol v2.6.5</span>
                    <span x-show="lang === 'hi'" style="display: none;">सुरक्षित लैबोरेटरी मैनेजमेंट प्रोटोकॉल v2.6.5</span>
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-white border shadow-sm px-4 fw-bold text-dark rounded-3" data-bs-dismiss="modal">
                        <span x-show="lang === 'en'">Dismiss Panel</span>
                        <span x-show="lang === 'hi'" style="display: none;">बंद करें</span>
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm fw-bold rounded-3" onclick="window.print()">
                        <span x-show="lang === 'en'">Print Full Manual</span>
                        <span x-show="lang === 'hi'" style="display: none;">पूरा मैन्युअल प्रिंट करें</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .documentation-nav .nav-link {
        border: 1px solid transparent; transition: all 0.2s ease;
        padding-top: 10px; padding-bottom: 10px; font-size: 13px; color: #4b5563 !important;
    }
    .documentation-nav .nav-link:hover { background: rgba(var(--bs-primary-rgb), 0.05); border-color: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary) !important; }
    .documentation-nav .nav-link.active { background: var(--bs-primary) !important; color: white !important; box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.2); }
    .w-8 { width: 2rem; } .h-8 { height: 2rem; }
    .fs-10 { font-size: 10px; } .fs-11 { font-size: 11px; } .fs-12 { font-size: 12px; } .fs-13 { font-size: 13px; } .fs-14 { font-size: 14px; } .fs-16 { font-size: 16px; }
    .bg-soft-primary { background-color: rgba(var(--bs-primary-rgb), 0.08); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.08); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08); }
    .text-white-70 { color: rgba(255, 255, 255, 0.7); }

    /* PRINT STYLES - Optimized for High Quality A4 Manual */
    @media print {
        @page { size: A4; margin: 15mm; }
        
        /* Hide entire page except the modal */
        body * { visibility: hidden; }
        #testDocumentationModal, #testDocumentationModal * { visibility: visible; }
        
        #testDocumentationModal {
            position: absolute !important;
            left: 0 !important; top: 0 !important;
            width: 100% !important;
            overflow: visible !important;
            display: block !important;
        }

        .modal-dialog { max-width: 100% !important; width: 100% !important; margin: 0 !important; }
        .modal-content { border: none !important; box-shadow: none !important; background: white !important; overflow: visible !important; }
        .modal-body { overflow: visible !important; padding: 0 !important; }
        
        /* UI Cleanup */
        .modal-header .btn-close, .modal-footer .d-flex.gap-2, .col-md-3, .nav-link, .btn, [data-bs-dismiss="modal"] { display: none !important; }
        
        .col-md-9 {
            width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important;
            padding: 0 !important; margin: 0 !important;
        }

        body { background: white !important; color: black !important; font-size: 11pt; }

        .modal-header {
            background-color: #0f172a !important; 
            color: #ffffff !important; 
            border-bottom: 4pt solid #0d6efd !important; 
            display: block !important; 
            padding: 40px !important; 
            -webkit-print-color-adjust: exact;
            border-radius: 0 !important;
        }

        .modal-header .modal-title { font-size: 26pt !important; color: #ffffff !important; }
        
        h2, h3 { 
            background: #f1f5f9 !important; 
            border-left: 6pt solid #0d6efd !important; 
            padding: 10px 15px !important; 
            margin-top: 30px !important; 
            font-size: 18pt !important; 
            -webkit-print-color-adjust: exact;
        }

        .card, .p-4, .border, .table-responsive, .accordion-item { page-break-inside: avoid; margin-bottom: 15px !important; }

        /* Icon reset for print */
        i { color: #0d6efd !important; -webkit-print-color-adjust: exact; }

        /* Ensure whichever language is selected, it prints that one only */
        [style*="display: none"] { display: none !important; }
        [x-show] { display: block !important; }
    }
</style>