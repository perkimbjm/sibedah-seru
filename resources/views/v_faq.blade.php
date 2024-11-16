<section id="faq">
    <div class="container mx-10">
        <h1 class="text-center mt-3 mb-1 fw-bold" style="letter-spacing: 5px;"><img src="{{ asset('img/landingpage/faq.png') }}" alt="">F.A.Q.</h1>
        <h6 class="text-center mb-2 fst-italic">( Frequently Asked Questions )</h6>
        <style>
            /* ACCORDION FAQ*/
            .accordion {
                border-radius: 20px;
            }

            .accordion-header :hover {
                background-color: #7db7f1;
            }
        </style>
        <div class="accordion" id="accordionExample">
            

            {{-- FAQ 1 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    {{ trans('frontend.faq.ask1') }}
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      {{ trans('frontend.faq.answer1') }}  
                    </div>
                </div>
            </div>
            {{-- END FAQ 1 --}}

            {{-- FAQ 2 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      {{ trans('frontend.faq.ask2') }}  
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      {{ trans('frontend.faq.answer2') }}  
                    </div>
                </div>
            </div>
            {{-- END FAQ 2 --}}

            {{-- FAQ 3 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                      {{ trans('frontend.faq.ask3') }} 
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        {{ trans('frontend.faq.answer3') }}
                        <ul class="d-block mx-3">
                            <li style="list-style-type: circle;">{{ trans('frontend.faq.answer3a') }}</li>
                            <li style="list-style-type: circle;">{{ trans('frontend.faq.answer3b') }}</li>
                            <li style="list-style-type: circle;">{{ trans('frontend.faq.answer3c') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- END FAQ 3 --}}

            {{-- FAQ 4 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseThree">
                       {{ trans('frontend.faq.ask4') }}
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                      {{ trans('frontend.faq.answer4') }}  
                    </div>
                </div>
            </div>
            {{-- END FAQ 4 --}}

            {{-- FAQ 5 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseThree">
                      {{ trans('frontend.faq.ask5') }}  
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        {{ trans('frontend.faq.answer5') }} 
                    </div>
                </div>
            </div>
            {{-- END FAQ 5 --}}


        </div>
    </div>
</section>