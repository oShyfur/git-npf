<!-- Footer -->
<footer class="page-footer font-small bg-primary pt-4 mt-4">


    <div class="container bottom_border">
        <div class="row">



            {% if footermenu.menuItems %}

            <div class="d-flex justify-content-between">

                {% for item in footermenu.menuItems if not item.viewBag.isHidden %}

                <div class="col-md-4 col-sm-6 col-xs-12 col-lg-2 mb-3 mt-3">
                    <div class="footer-menu text-center text-sm-left">
                        <h5 class="headin5_amrc gr-1">{{ item.title }}</h5>
                        <!--headin5_amrc-->


                        {% if item.items %}

                        <ul class="footer_ul_amrc">

                            {% for subitem in item.items %}

                            <li>
                                <a href="{{ subitem.url }}" {{ subitem.viewBag.isExternal ? 'target="_blank"' }}>{{ subitem.title }}</a>
                            </li>

                            {% endfor %}

                        </ul>
                        {% endif %}
                    </div>

                </div>

                {% endfor %}

            </div>


            {% endif %}



        </div>
    </div>




</footer>
<!-- Footer -->