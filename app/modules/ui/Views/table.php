<?php
ob_start();
?>


<div class="row">
    <div class="col-lg-9">
        <div class="table-responsive table--no-card m-b-30">
            <table class="table table-borderless table-striped table-earning">
                <thead>
                    <tr>
                        <th>date</th>
                        <th>order ID</th>
                        <th>name</th>
                        <th class="text-end">price</th>
                        <th class="text-end">quantity</th>
                        <th class="text-end">total</th>
                        <th>customer</th>
                        <th>status</th>
                        <th>payment method</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100398</td>
                        <td>iPhone 17 128GB Titanium</td>
                        <td class="text-end">$999.00</td>
                        <td class="text-end">1</td>
                        <td class="text-end">$999.00</td>
                        <td>John Smith</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>Credit Card</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100397</td>
                        <td>Samsung Galaxy S25 Ultra</td>
                        <td class="text-end">$756.00</td>
                        <td class="text-end">1</td>
                        <td class="text-end">$756.00</td>
                        <td>Jane Doe</td>
                        <td><span class="badge bg-warning">Processing</span></td>
                        <td>PayPal</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100396</td>
                        <td>Game Console Controller</td>
                        <td class="text-end">$22.00</td>
                        <td class="text-end">2</td>
                        <td class="text-end">$44.00</td>
                        <td>Mike Johnson</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>Bank Transfer</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100395</td>
                        <td>iPhone 17 Pro Max 1TB Space Black</td>
                        <td class="text-end">$1199.00</td>
                        <td class="text-end">1</td>
                        <td class="text-end">$1199.00</td>
                        <td>Sarah Wilson</td>
                        <td><span class="badge bg-danger">Cancelled</span></td>
                        <td>Apple Pay</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100393</td>
                        <td>USB-C to USB-C Thunderbolt 5 Cable</td>
                        <td class="text-end">$10.00</td>
                        <td class="text-end">3</td>
                        <td class="text-end">$30.00</td>
                        <td>David Chen</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>Google Pay</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100392</td>
                        <td>Smartwatch 4.0 LTE Wifi</td>
                        <td class="text-end">$199.00</td>
                        <td class="text-end">6</td>
                        <td class="text-end">$1494.00</td>
                        <td>Emily Rodriguez</td>
                        <td><span class="badge bg-info">Shipped</span></td>
                        <td>Debit Card</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>100391</td>
                        <td>Camera C430W 4k</td>
                        <td class="text-end">$699.00</td>
                        <td class="text-end">1</td>
                        <td class="text-end">$699.00</td>
                        <td>Robert Taylor</td>
                        <td><span class="badge bg-warning">Processing</span></td>
                        <td>Crypto</td>
                    </tr>
                    <tr>
                        <td>2025-01-14 16:22</td>
                        <td>100390</td>
                        <td>Wireless Earbuds Pro Max</td>
                        <td class="text-end">$249.00</td>
                        <td class="text-end">2</td>
                        <td class="text-end">$498.00</td>
                        <td>Lisa Anderson</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>PayPal</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="au-card au-card--bg-blue au-card-top-countries m-b-30">
            <div class="au-card-inner">
                <div class="table-responsive">
                    <table class="table table-top-countries">
                        <tbody>
                            <tr>
                                <td>United States</td>
                                <td class="text-end">$119,366.96</td>
                            </tr>
                            <tr>
                                <td>Australia</td>
                                <td class="text-end">$70,261.65</td>
                            </tr>
                            <tr>
                                <td>United Kingdom</td>
                                <td class="text-end">$46,399.22</td>
                            </tr>
                            <tr>
                                <td>Turkey</td>
                                <td class="text-end">$35,364.90</td>
                            </tr>
                            <tr>
                                <td>Germany</td>
                                <td class="text-end">$20,366.96</td>
                            </tr>
                            <tr>
                                <td>France</td>
                                <td class="text-end">$10,366.96</td>
                            </tr>
                            <tr>
                                <td>Australia</td>
                                <td class="text-end">$5,366.96</td>
                            </tr>
                            <tr>
                                <td>Italy</td>
                                <td class="text-end">$1639.32</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <!-- USER DATA-->
        <div class="user-data m-b-30">
            <h3 class="title-3 m-b-30">
                <i class="zmdi zmdi-account-calendar"></i>user data
            </h3>
            <div class="filters m-b-45">
                <div class="select-wrapper">
                    <select class="form-select form-select-lg" name="property">
                        <option selected>All Properties</option>
                        <option value="">Products</option>
                        <option value="">Services</option>
                    </select>

                </div>
                <div class="select-wrapper">
                    <select class="form-select form-select-lg" name="time">
                        <option selected value="all">All Time</option>
                        <option value="month">By Month</option>
                        <option value="day">By Day</option>
                    </select>

                </div>
            </div>
            <div class="table-responsive table-data">
                <table class="table">
                    <thead>
                        <tr>
                            <td>
                                <label class="au-checkbox">
                                    <input type="checkbox">
                                    <span class="au-checkmark"></span>
                                </label>
                            </td>
                            <td>name</td>
                            <td>role</td>
                            <td>type</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label class="au-checkbox">
                                    <input type="checkbox">
                                    <span class="au-checkmark"></span>
                                </label>
                            </td>
                            <td>
                                <div class="table-data__info">
                                    <h6>lori lynch</h6>
                                    <span>
                                        <a href="#">johndoe@gmail.com</a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="role admin">admin</span>
                            </td>
                            <td>
                                <div class="select-wrapper">
                                    <select class="form-select form-select-lg" name="property">
                                        <option selected>Full Control</option>
                                        <option value="">Post</option>
                                        <option value="">Watch</option>
                                    </select>

                                </div>
                            </td>
                            <td>
                                <span class="more">
                                    <i class="zmdi zmdi-more"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="au-checkbox">
                                    <input type="checkbox" checked>
                                    <span class="au-checkmark"></span>
                                </label>
                            </td>
                            <td>
                                <div class="table-data__info">
                                    <h6>lori lynch</h6>
                                    <span>
                                        <a href="#">johndoe@gmail.com</a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="role user">user</span>
                            </td>
                            <td>
                                <div class="select-wrapper">
                                    <select class="form-select form-select-lg" name="property">
                                        <option value="">Full Control</option>
                                        <option value="" selected>Post</option>
                                        <option value="">Watch</option>
                                    </select>

                                </div>
                            </td>
                            <td>
                                <span class="more">
                                    <i class="zmdi zmdi-more"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="au-checkbox">
                                    <input type="checkbox">
                                    <span class="au-checkmark"></span>
                                </label>
                            </td>
                            <td>
                                <div class="table-data__info">
                                    <h6>lori lynch</h6>
                                    <span>
                                        <a href="#">johndoe@gmail.com</a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="role user">user</span>
                            </td>
                            <td>
                                <div class="select-wrapper">
                                    <select class="form-select form-select-lg" name="property">
                                        <option value="">Full Control</option>
                                        <option value="" selected>Post</option>
                                        <option value="">Watch</option>
                                    </select>

                                </div>
                            </td>
                            <td>
                                <span class="more">
                                    <i class="zmdi zmdi-more"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="au-checkbox">
                                    <input type="checkbox">
                                    <span class="au-checkmark"></span>
                                </label>
                            </td>
                            <td>
                                <div class="table-data__info">
                                    <h6>lori lynch</h6>
                                    <span>
                                        <a href="#">johndoe@gmail.com</a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="role member">member</span>
                            </td>
                            <td>
                                <div class="select-wrapper">
                                    <select class="form-select form-select-lg" name="property">
                                        <option selected>Full Control</option>
                                        <option value="">Post</option>
                                        <option value="">Watch</option>
                                    </select>

                                </div>
                            </td>
                            <td>
                                <span class="more">
                                    <i class="zmdi zmdi-more"></i>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="user-data__footer">
                <button class="au-btn au-btn-load">load more</button>
            </div>
        </div>
        <!-- END USER DATA-->
    </div>
    <div class="col-lg-6">
        <!-- TOP CAMPAIGN-->
        <div class="top-campaign">
            <h3 class="title-3 m-b-30">top campaigns</h3>
            <div class="table-responsive">
                <table class="table table-top-campaign">
                    <tbody>
                        <tr>
                            <td>1. Australia</td>
                            <td>$70,261.65</td>
                        </tr>
                        <tr>
                            <td>2. United Kingdom</td>
                            <td>$46,399.22</td>
                        </tr>
                        <tr>
                            <td>3. Turkey</td>
                            <td>$35,364.90</td>
                        </tr>
                        <tr>
                            <td>4. Germany</td>
                            <td>$20,366.96</td>
                        </tr>
                        <tr>
                            <td>5. France</td>
                            <td>$10,366.96</td>
                        </tr>
                        <tr>
                            <td>3. Turkey</td>
                            <td>$35,364.90</td>
                        </tr>
                        <tr>
                            <td>4. Germany</td>
                            <td>$20,366.96</td>
                        </tr>
                        <tr>
                            <td>5. France</td>
                            <td>$10,366.96</td>
                        </tr>
                        <tr>
                            <td>3. Turkey</td>
                            <td>$35,364.90</td>
                        </tr>
                        <tr>
                            <td>4. Germany</td>
                            <td>$20,366.96</td>
                        </tr>
                        <tr>
                            <td>5. France</td>
                            <td>$10,366.96</td>
                        </tr>
                        <tr>
                            <td>4. Germany</td>
                            <td>$20,366.96</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--  END TOP CAMPAIGN-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <!-- DATA TABLE -->
        <h3 class="title-5 m-b-35">data table</h3>
        <div class="table-data__tool">
            <div class="table-data__tool-left">
                <div class="select-wrapper">
                    <select class="form-select form-select-lg" name="property">
                        <option selected>All Properties</option>
                        <option value="option1">Option 1</option>
                        <option value="option2">Option 2</option>
                    </select>

                </div>
                <div class="select-wrapper">
                    <select class="form-select form-select-lg" name="time">
                        <option selected>Today</option>
                        <option value="">3 Days</option>
                        <option value="">1 Week</option>
                    </select>

                </div>
                <button class="au-btn-filter">
                    <i class="zmdi zmdi-filter-list"></i>filters</button>
            </div>
            <div class="table-data__tool-right">
                <button class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i>add item</button>
                <div class="select-wrapper">
                    <select class="form-select form-select-lg" name="type">
                        <option selected>Export</option>
                        <option value="option1">Option 1</option>
                        <option value="option2">Option 2</option>
                    </select>

                </div>
            </div>
        </div>
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>
                            <label class="au-checkbox">
                                <input type="checkbox">
                                <span class="au-checkmark"></span>
                            </label>
                        </th>
                        <th>name</th>
                        <th>email</th>
                        <th>description</th>
                        <th>date</th>
                        <th>status</th>
                        <th>price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="tr-shadow">
                        <td>
                            <label class="au-checkbox">
                                <input type="checkbox">
                                <span class="au-checkmark"></span>
                            </label>
                        </td>
                        <td>Lori Lynch</td>
                        <td>
                            <span class="block-email">lori@example.com</span>
                        </td>
                        <td class="desc">Samsung Galaxy S25 Ultra</td>
                        <td>2025-01-15 14:32</td>
                        <td>
                            <span class="status--process">Processed</span>
                        </td>
                        <td>$679.00</td>
                        <td>
                            <div class="table-data-feature">
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Send">
                                    <i class="zmdi zmdi-mail-send"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="More">
                                    <i class="zmdi zmdi-more"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="spacer"></tr>
                    <tr class="tr-shadow">
                        <td>
                            <label class="au-checkbox">
                                <input type="checkbox">
                                <span class="au-checkmark"></span>
                            </label>
                        </td>
                        <td>Lori Lynch</td>
                        <td>
                            <span class="block-email">john@example.com</span>
                        </td>
                        <td class="desc">iPhone 17 128GB Titanium</td>
                        <td>2025-01-15 14:32</td>
                        <td>
                            <span class="status--process">Processed</span>
                        </td>
                        <td>$999.00</td>
                        <td>
                            <div class="table-data-feature">
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Send">
                                    <i class="zmdi zmdi-mail-send"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="More">
                                    <i class="zmdi zmdi-more"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="spacer"></tr>
                    <tr class="tr-shadow">
                        <td>
                            <label class="au-checkbox">
                                <input type="checkbox">
                                <span class="au-checkmark"></span>
                            </label>
                        </td>
                        <td>Lori Lynch</td>
                        <td>
                            <span class="block-email">lyn@example.com</span>
                        </td>
                        <td class="desc">iPhone 17 Pro Max 1TB Space Black</td>
                        <td>2025-01-15 14:32</td>
                        <td>
                            <span class="status--denied">Denied</span>
                        </td>
                        <td>$1199.00</td>
                        <td>
                            <div class="table-data-feature">
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Send">
                                    <i class="zmdi zmdi-mail-send"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="More">
                                    <i class="zmdi zmdi-more"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="spacer"></tr>
                    <tr class="tr-shadow">
                        <td>
                            <label class="au-checkbox">
                                <input type="checkbox">
                                <span class="au-checkmark"></span>
                            </label>
                        </td>
                        <td>Lori Lynch</td>
                        <td>
                            <span class="block-email">doe@example.com</span>
                        </td>
                        <td class="desc">Camera C430W 4k</td>
                        <td>2025-01-15 14:32</td>
                        <td>
                            <span class="status--process">Processed</span>
                        </td>
                        <td>$699.00</td>
                        <td>
                            <div class="table-data-feature">
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Send">
                                    <i class="zmdi zmdi-mail-send"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                                <button class="item" data-bs-toggle="tooltip" data-bs-placement="top" title="More">
                                    <i class="zmdi zmdi-more"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- END DATA TABLE -->
    </div>
</div>
<div class="row m-t-30">
    <div class="col-md-12">
        <!-- DATA TABLE-->
        <div class="table-responsive m-b-40">
            <table class="table table-borderless table-data3">
                <thead>
                    <tr>
                        <th>date</th>
                        <th>type</th>
                        <th>description</th>
                        <th>status</th>
                        <th>price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Mobile</td>
                        <td>iPhone 17 128GB Titanium</td>
                        <td class="process">Processed</td>
                        <td>$999.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Mobile</td>
                        <td>Samsung Galaxy S25 Ultra</td>
                        <td class="process">Processed</td>
                        <td>$756.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Game</td>
                        <td>Game Console Controller</td>
                        <td class="denied">Denied</td>
                        <td>$22.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Mobile</td>
                        <td>iPhone 17 Pro Max 1TB Space Black</td>
                        <td class="denied">Denied</td>
                        <td>$1199.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Accessories</td>
                        <td>USB-C to USB-C Thunderbolt 5 Cable</td>
                        <td class="process">Processed</td>
                        <td>$10.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Accesories</td>
                        <td>Smartwatch 4.0 LTE Wifi</td>
                        <td class="denied">Denied</td>
                        <td>$199.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Camera</td>
                        <td>Camera C430W 4k</td>
                        <td class="process">Processed</td>
                        <td>$699.00</td>
                    </tr>
                    <tr>
                        <td>2025-01-15 14:32</td>
                        <td>Computer</td>
                        <td>Macbook Pro Retina 2017</td>
                        <td class="process">Processed</td>
                        <td>$10.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- END DATA TABLE-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="copyright">
            <p>Copyright © 2025 Colorlib. All rights reserved. Template by <a href="https://colorlib.com" rel="nofollow"
                    target="_blank">Colorlib</a>.</p>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>