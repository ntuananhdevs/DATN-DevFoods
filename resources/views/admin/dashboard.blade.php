@extends('admin.layouts.layout')

@section('title', 'Dashboard')

@section('content')
    <!-- Congratulations Card -->
    <div class="widget congratulations">
        <h2>Congratulations John,</h2>
        <p>You have done 57.6% more sales today. Check your new badge in your profile.</p>
    </div>

    <!-- Subscribers Gained -->
    <div class="widget stats">
        <h3>Subscribers Gained</h3>
        <div class="value">92.6K</div>
        <div class="chart"></div>
    </div>

    <!-- Orders Received -->
    <div class="widget stats">
        <h3>Orders Received</h3>
        <div class="value">97.5K</div>
        <div class="chart"></div>
    </div>

    <!-- Avg Sessions -->
    <div class="widget avg-sessions">
        <h3>Avg. Sessions</h3>
        <div class="value">2.7K</div>
        <div class="change">+5.2% vs last 7 days</div>
        <div class="chart">
            <div class="bar secondary" style="height: 40%"></div>
            <div class="bar" style="height: 80%"></div>
            <div class="bar secondary" style="height: 60%"></div>
            <div class="bar" style="height: 70%"></div>
            <div class="bar secondary" style="height: 30%"></div>
        </div>
        <a href="#" class="button">View Details <i class="fas fa-chevron-right"></i></a>
        <div class="info">
            <div>
                <p>Goal: $100000</p>
                <div class="progress" style="width: 50%"></div>
            </div>
            <div>
                <p>Users: 100K</p>
                <div class="progress" style="width: 70%; background-color: #ff9f43;"></div>
            </div>
            <div>
                <p>Retention: 90%</p>
                <div class="progress" style="width: 90%"></div>
            </div>
            <div>
                <p>Duration: 1yr</p>
                <div class="progress" style="width: 60%"></div>
            </div>
        </div>
    </div>

    <!-- Support Tracker -->
    <div class="widget support-tracker">
        <h3>Support Tracker</h3>
        <div class="value">163 Tickets</div>
    </div>

    <!-- Support Tracker (Additional Stats) -->
    <div class="widget support-tracker-stats">
        <div class="stats-item">
            <h3>New Tickets</h3>
            <div class="value">29</div>
        </div>
        <div class="stats-item">
            <h3>Open Tickets</h3>
            <div class="value">63</div>
        </div>
        <div class="stats-item">
            <h3>Response Time</h3>
            <div class="value">1d</div>
        </div>
    </div>

    <!-- Product Orders -->
    <div class="widget product-orders">
        <h3>Product Orders</h3>
        <div class="chart-container">
            <div class="circle">
                <div class="value">42459</div>
            </div>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="dot finished"></span> Finished: 25043
            </div>
            <div class="legend-item">
                <span class="dot pending"></span> Pending: 14658
            </div>
            <div class="legend-item">
                <span class="dot rejected"></span> Rejected: 4758
            </div>
        </div>
    </div>

    <!-- Sales Stats -->
    <div class="widget sales-stats">
        <h3>Sales Stats</h3>
        <div class="chart-container">
            <div class="radar">
                <div class="radar-sales"></div>
                <div class="radar-visit"></div>
            </div>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="dot sales"></span> Sales
            </div>
            <div class="legend-item">
                <span class="dot visit"></span> Visit
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="widget activity-timeline">
        <h3>Activity Timeline</h3>
        <ul class="timeline">
            <li>
                <div class="timeline-icon client-meeting"><i class="fas fa-plus"></i></div>
                <div class="timeline-content">
                    <h4>Client Meeting</h4>
                    <p>Bonbon macaroon jelly beans gummy bears jelly lollipop apple</p>
                    <span>25 mins ago</span>
                </div>
            </li>
            <li>
                <div class="timeline-icon email-newsletter"><i class="fas fa-envelope"></i></div>
                <div class="timeline-content">
                    <h4>Email Newsletter</h4>
                    <p>Cupcake gummi bears soufflé caramels candy</p>
                    <span>15 days ago</span>
                </div>
            </li>
            <li>
                <div class="timeline-icon plan-webinar"><i class="fas fa-exclamation-circle"></i></div>
                <div class="timeline-content">
                    <h4>Plan Webinar</h4>
                    <p>Candy ice cream cake. Halvah gummi bears</p>
                    <span>20 days ago</span>
                </div>
            </li>
            <li>
                <div class="timeline-icon launch-website"><i class="fas fa-check-circle"></i></div>
                <div class="timeline-content">
                    <h4>Launch Website</h4>
                    <p>Candy ice cream cake.</p>
                    <span>25 days ago</span>
                </div>
            </li>
            <li>
                <div class="timeline-icon marketing"><i class="fas fa-bullhorn"></i></div>
                <div class="timeline-content">
                    <h4>Marketing</h4>
                    <p>Candy ice cream. Halvah gummi bears cupcake gummi bears.</p>
                    <span>28 days ago</span>
                </div>
            </li>
        </ul>
    </div>

    <!-- Dispatched Orders -->
    <div class="widget dispatched-orders">
        <h3>Dispatched Orders</h3>
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Operators</th>
                    <th>Location</th>
                    <th>Distance</th>
                    <th>Start Date</th>
                    <th>Est Del. Dt</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#879985</td>
                    <td><span class="status moving">Moving</span></td>
                    <td>
                        <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Operator">
                        <img src="https://randomuser.me/api/portraits/women/3.jpg" alt="Operator">
                    </td>
                    <td>Anniston, Alabama</td>
                    <td><div class="distance moving" style="width: 130px;"></div> 130 km</td>
                    <td>14:58 26/07/2018</td>
                    <td>28/07/2018</td>
                </tr>
                <tr>
                    <td>#156897</td>
                    <td><span class="status pending">Pending</span></td>
                    <td>
                        <img src="https://randomuser.me/api/portraits/men/4.jpg" alt="Operator">
                        <img src="https://randomuser.me/api/portraits/women/5.jpg" alt="Operator">
                    </td>
                    <td>Cordova, Alaska</td>
                    <td><div class="distance pending" style="width: 234px;"></div> 234 km</td>
                    <td>14:58 26/07/2018</td>
                    <td>28/07/2018</td>
                </tr>
                <tr>
                    <td>#568975</td>
                    <td><span class="status moving">Moving</span></td>
                    <td>
                        <img src="https://randomuser.me/api/portraits/men/6.jpg" alt="Operator">
                        <img src="https://randomuser.me/api/portraits/women/7.jpg" alt="Operator">
                    </td>
                    <td>Florence, Alabama</td>
                    <td><div class="distance moving" style="width: 166px;"></div> 166 km</td>
                    <td>14:58 26/07/2018</td>
                    <td>28/07/2018</td>
                </tr>
                <tr>
                    <td>#4589</td>
                    <td><span class="status cancelled">Cancelled</span></td>
                    <td>
                        <img src="https://randomuser.me/api/portraits/men/8.jpg" alt="Operator">
                    </td>
                    <td>Clifton, Arizona</td>
                    <td><div class="distance cancelled" style="width: 123px;"></div> 123 km</td>
                    <td>14:58 26/07/2018</td>
                    <td>28/07/2018</td>
                </tr>
            </tbody>
        </table>
    </div>

    <style>
        /* Main Content Area */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 40px 20px 20px 20px;
            background-color: #ffffff;
            min-height: calc(100vh - 60px);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 20px;
            grid-template-areas: 
                "congratulations subscribers orders-received"
                "avg-sessions avg-sessions support-tracker"
                "support-stats product-orders sales-stats"
                "activity-timeline activity-timeline dispatched-orders";
        }

        /* Widget Styles */
        .widget {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            color: #5e5873;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Congratulations Card */
        .congratulations {
            grid-area: congratulations;
            background: linear-gradient(135deg, #5e50ee, #9b8cff);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .congratulations::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/confetti.png');
            opacity: 0.2;
        }

        .congratulations h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .congratulations p {
            font-size: 14px;
            color: #e0e0e0;
        }

        /* Subscribers Gained and Orders Received */
        .stats {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stats h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .stats .value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stats .chart {
            height: 50px;
            background: linear-gradient(90deg, #5e50ee, #f8f9fa);
            border-radius: 5px;
        }

        .subscribers {
            grid-area: subscribers;
        }

        .orders-received {
            grid-area: orders-received;
        }

        /* Avg Sessions */
        .avg-sessions {
            grid-area: avg-sessions;
        }

        .avg-sessions h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .avg-sessions .value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .avg-sessions .change {
            color: #28c76f;
            font-size: 14px;
        }

        .avg-sessions .chart {
            display: flex;
            justify-content: space-between;
            height: 80px;
            margin: 20px 0;
        }

        .avg-sessions .bar {
            width: 15%;
            background-color: #5e50ee;
            border-radius: 5px;
        }

        .avg-sessions .bar.secondary {
            background-color: #d1d5db;
            opacity: 0.5;
        }

        .avg-sessions .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #5e50ee;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 10px;
        }

        .avg-sessions .info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6e6b7b;
            margin-top: 20px;
        }

        .avg-sessions .info .progress {
            height: 5px;
            border-radius: 5px;
            background-color: #5e50ee;
        }

        /* Support Tracker */
        .support-tracker {
            grid-area: support-tracker;
        }

        .support-tracker h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .support-tracker .value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        /* Support Tracker Stats */
        .support-tracker-stats {
            grid-area: support-stats;
            display: flex;
            justify-content: space-between;
        }

        .support-tracker-stats .stats-item {
            text-align: center;
        }

        .support-tracker-stats h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .support-tracker-stats .value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        /* Product Orders */
        .product-orders {
            grid-area: product-orders;
        }

        .product-orders h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .product-orders .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 150px;
        }

        .product-orders .circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(#5e50ee 0% 59%, #ff9f43 59% 94%, #ff6b6b 94% 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .product-orders .circle::before {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background-color: #f8f9fa;
            border-radius: 50%;
        }

        .product-orders .value {
            position: relative;
            font-size: 24px;
            font-weight: bold;
        }

        .product-orders .legend {
            margin-top: 10px;
            font-size: 14px;
        }

        .product-orders .legend-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }

        .product-orders .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .product-orders .dot.finished {
            background-color: #5e50ee;
        }

        .product-orders .dot.pending {
            background-color: #ff9f43;
        }

        .product-orders .dot.rejected {
            background-color: #ff6b6b;
        }

        /* Sales Stats */
        .sales-stats {
            grid-area: sales-stats;
        }

        .sales-stats h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .sales-stats .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 150px;
        }

        .sales-stats .radar {
            width: 120px;
            height: 120px;
            position: relative;
        }

        .sales-stats .radar-sales {
            position: absolute;
            width: 100%;
            height: 100%;
            background: conic-gradient(#5e50ee 0% 30%, transparent 30% 100%);
            clip-path: polygon(50% 50%, 50% 0%, 80% 20%, 80% 80%, 20% 80%, 20% 20%);
            opacity: 0.5;
        }

        .sales-stats .radar-visit {
            position: absolute;
            width: 100%;
            height: 100%;
            background: conic-gradient(#28c76f 0% 40%, transparent 40% 100%);
            clip-path: polygon(50% 50%, 50% 0%, 70% 30%, 70% 70%, 30% 70%, 30% 30%);
            opacity: 0.5;
        }

        .sales-stats .legend {
            margin-top: 10px;
            font-size: 14px;
            display: flex;
            justify-content: center;
        }

        .sales-stats .legend-item {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }

        .sales-stats .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .sales-stats .dot.sales {
            background-color: #5e50ee;
        }

        .sales-stats .dot.visit {
            background-color: #28c76f;
        }

        /* Activity Timeline */
        .activity-timeline {
            grid-area: activity-timeline;
        }

        .activity-timeline h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .activity-timeline .timeline {
            list-style: none;
            padding: 0;
            margin: 10px 0;
            position: relative;
        }

        .activity-timeline .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #d1d5db;
        }

        .activity-timeline .timeline li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            position: relative;
        }

        .activity-timeline .timeline-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 14px;
            margin-right: 10px;
            z-index: 1;
        }

        .activity-timeline .timeline-icon.client-meeting {
            background-color: #5e50ee;
        }

        .activity-timeline .timeline-icon.email-newsletter {
            background-color: #ff9f43;
        }

        .activity-timeline .timeline-icon.plan-webinar {
            background-color: #ff6b6b;
        }

        .activity-timeline .timeline-icon.launch-website {
            background-color: #28c76f;
        }

        .activity-timeline .timeline-icon.marketing {
            background-color: #5e50ee;
        }

        .activity-timeline .timeline-content h4 {
            font-size: 14px;
            color: #5e5873;
        }

        .activity-timeline .timeline-content p {
            font-size: 12px;
            color: #6e6b7b;
            margin: 5px 0;
        }

        .activity-timeline .timeline-content span {
            font-size: 12px;
            color: #a1a1a1;
        }

        /* Dispatched Orders */
        .dispatched-orders {
            grid-area: dispatched-orders;
        }

        .dispatched-orders h3 {
            font-size: 14px;
            color: #6e6b7b;
        }

        .dispatched-orders table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .dispatched-orders th, .dispatched-orders td {
            padding: 10px;
            text-align: left;
            font-size: 12px;
            color: #5e5873;
        }

        .dispatched-orders th {
            color: #6e6b7b;
            font-weight: normal;
        }

        .dispatched-orders td {
            border-bottom: 1px solid #e5e7eb;
        }

        .dispatched-orders .status {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            color: #fff;
        }

        .dispatched-orders .status.moving {
            background-color: #28c76f;
        }

        .dispatched-orders .status.pending {
            background-color: #ff9f43;
        }

        .dispatched-orders .status.cancelled {
            background-color: #ff6b6b;
        }

        .dispatched-orders img {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .dispatched-orders .distance {
            height: 5px;
            border-radius: 5px;
            display: inline-block;
            margin-right: 5px;
        }

        .dispatched-orders .distance.moving {
            background-color: #28c76f;
        }

        .dispatched-orders .distance.pending {
            background-color: #ff9f43;
        }

        .dispatched-orders .distance.cancelled {
            background-color: #ff6b6b;
        }
    </style>
@endsection