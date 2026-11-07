<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị hệ thống') - SkyBooking</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #062b55;
            --navy-light: #0b4278;
            --blue: #1267a5;
            --gold: #d7ae58;
            --gold-light: #f5e5bd;
            --bg: #f4f7fb;
            --text: #1c2f46;
            --muted: #77869a;
            --border: #e4eaf2;
            --white: #ffffff;
            --sidebar-width: 250px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Be Vietnam Pro', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 13px;
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-width);
            padding: 30px 18px 20px;
            background: linear-gradient(180deg,
                    #06264b,
                    #073a6b 75%,
                    #052544);
            color: white;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 8px 0 30px rgba(6, 43, 85, .08);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 28px;
            border-bottom: 1px solid rgba(255, 255, 255, .13);
            color: white;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: var(--gold);
            color: var(--navy);
            border-radius: 14px;
            font-size: 24px;
            box-shadow: 0 5px 18px rgba(215, 174, 88, .2);
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .brand-name span {
            color: var(--gold);
        }

        .brand-caption {
            color: #b8cce0;
            font-size: 9px;
            letter-spacing: 1.4px;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .nav-caption {
            margin: 28px 12px 12px;
            color: #8da9c4;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .side-nav {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .side-link {
            min-height: 47px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            color: #d3e0ed;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            transition: background .2s, color .2s, transform .2s;
        }

        .side-link:hover {
            color: white;
            background: rgba(255, 255, 255, .09);
            transform: translateX(2px);
        }

        .side-link.active {
            color: white;
            background: linear-gradient(100deg,
                    rgba(215, 174, 88, .24),
                    rgba(215, 174, 88, .08));
            box-shadow: inset 3px 0 var(--gold);
        }

        .side-icon {
            width: 22px;
            flex-shrink: 0;
            text-align: center;
            font-size: 18px;
        }

        .sidebar-spacer {
            flex: 1;
            min-height: 25px;
        }

        .sidebar-promo {
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .06);
            border-radius: 14px;
            padding: 17px 15px;
            margin-top: 25px;
        }

        .sidebar-promo h3 {
            font-size: 12px;
            color: var(--gold);
            margin-bottom: 9px;
        }

        .sidebar-promo p {
            font-size: 11px;
            color: #c1d2e3;
            line-height: 1.8;
        }

        .sidebar-bottom {
            border-top: 1px solid rgba(255, 255, 255, .12);
            margin-top: 20px;
            padding: 15px 10px 0;
            color: #9fb6cc;
            font-size: 10px;
            line-height: 1.9;
        }

        /* MAIN */

        .main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            min-width: 0;
        }

        .topbar {
            min-height: 82px;
            padding: 17px 4.5%;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
        }

        .topbar-eyebrow {
            color: var(--muted);
            font-size: 10px;
            margin-bottom: 7px;
            letter-spacing: .5px;
        }

        .topbar-title {
            color: var(--navy);
            font-size: 17px;
            font-weight: 700;
        }

        .account-area {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .welcome {
            color: #566a80;
            font-size: 11px;
            margin-right: 5px;
        }

        .top-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 39px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--navy);
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .top-button:hover {
            border-color: var(--gold);
            background: #fffaf0;
        }

        .top-button.primary {
            background: var(--navy);
            color: white;
            border-color: var(--navy);
        }

        .top-button.primary:hover {
            background: var(--navy-light);
        }

        .logout-form {
            display: inline-flex;
        }

        .content {
            padding: 35px 4.5%;
            min-width: 0;
        }

        /* PAGE HEADERS */

        .page-heading,
        .page-header {
            margin-bottom: 24px;
        }

        .page-heading small {
            color: #a37b2e;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
        }

        .page-heading h1,
        .page-header h1 {
            color: var(--navy);
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .page-heading p,
        .page-header p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.8;
            margin-top: 9px;
        }

        /* PANELS */

        .panel,
        .card {
            padding: 25px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .05);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .panel-header h2 {
            color: var(--navy);
            font-size: 17px;
        }

        /* BUTTONS */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 39px;
            padding: 10px 15px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: .2s;
        }

        .btn-primary {
            background: var(--navy);
            color: white;
        }

        .btn-primary:hover {
            background: var(--navy-light);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy);
        }

        .btn-gold:hover {
            background: #e8c57d;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #bb2535;
        }

        .btn-secondary {
            background: #e9eef5;
            color: var(--navy);
        }

        .btn-secondary:hover {
            background: #dce5ef;
        }

        /* TABLES */

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        th {
            background: #f4f7fb;
            color: var(--navy);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        tbody tr:hover {
            background: #fafbfd;
        }

        .airport-code {
            color: var(--navy);
            font-weight: 700;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .actions form {
            display: inline;
        }

        /* ALERTS */

        .alert {
            padding: 13px 16px;
            margin-bottom: 18px;
            border-radius: 9px;
            font-size: 12px;
            line-height: 1.7;
        }

        .alert-warning {
            color: #92400e;
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        .alert-success {
            color: #176b42;
            background: #e8f6ee;
            border: 1px solid #bde8d0;
        }

        .alert-error {
            color: #a42632;
            background: #fdecee;
            border: 1px solid #f5cccc;
        }

        /* FORMS */

        .form-group {
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--navy);
            font-size: 11px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            min-height: 44px;
            padding: 12px 13px;
            border: 1px solid #dce4ee;
            border-radius: 9px;
            background: white;
            color: var(--text);
            font-size: 12px;
            outline: none;
            transition: .2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3982b9;
            box-shadow: 0 0 0 3px rgba(18, 103, 165, .09);
        }

        .field-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 11px;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 25px;
        }


        /* PAGINATION */

        .pagination {
            margin-top: 20px;
        }

        /* Giới hạn kích thước icon Previous / Next */
        .pagination svg,
        nav[role="navigation"] svg {
            width: 18px !important;
            height: 18px !important;
            max-width: 18px;
            max-height: 18px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Bố cục thanh phân trang Laravel */
        nav[role="navigation"] {
            margin-top: 22px;
            font-size: 12px;
            color: var(--muted);
        }

        nav[role="navigation"] a {
            color: var(--navy);
            text-decoration: none;
        }

        nav[role="navigation"] a:hover {
            color: var(--blue);
        }

        /* Các phần tử chuyển trang */
        .pagination a,
        .pagination span {
            font-size: 12px;
        }


        /* RESPONSIVE */

        @media (max-width: 1100px) {
            :root {
                --sidebar-width: 215px;
            }

            .sidebar {
                padding-left: 13px;
                padding-right: 13px;
            }
        }

        @media (max-width: 760px) {
            :root {
                --sidebar-width: 0px;
            }

            .sidebar {
                position: relative;
                inset: auto;
                width: 100%;
                padding: 13px 16px;
                overflow: visible;
            }

            .brand {
                padding: 0 4px 13px;
            }

            .brand-icon {
                width: 38px;
                height: 38px;
                font-size: 21px;
            }

            .brand-name {
                font-size: 19px;
            }

            .nav-caption,
            .sidebar-promo,
            .sidebar-spacer,
            .sidebar-bottom {
                display: none;
            }

            .side-nav {
                flex-direction: row;
                overflow-x: auto;
                gap: 7px;
                padding-top: 12px;
            }

            .side-link {
                flex: 0 0 auto;
                min-height: 40px;
                padding: 9px 11px;
                gap: 7px;
                font-size: 11px;
                white-space: nowrap;
            }

            .side-link.active {
                box-shadow: inset 0 -2px var(--gold);
            }

            .side-icon {
                width: auto;
                font-size: 15px;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 16px 5%;
                min-height: 72px;
            }

            .welcome {
                display: none;
            }

            .top-button {
                min-height: 35px;
                padding: 8px 10px;
                font-size: 10px;
            }

            .content {
                padding: 24px 5%;
            }

            .panel,
            .card {
                padding: 18px;
            }

            .page-heading h1,
            .page-header h1 {
                font-size: 22px;
            }
        }

        @media (max-width: 400px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .account-area {
                justify-content: flex-start;
            }
        }


        /* AIRPORT MANAGEMENT */

        .airport-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .airport-summary-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
        }

        .airport-summary-card .summary-label {
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 12px;
        }

        .airport-summary-card .summary-value {
            color: var(--navy);
            font-size: 25px;
            font-weight: 800;
        }

        .airport-summary-card .summary-note {
            color: var(--muted);
            font-size: 11px;
            margin-top: 8px;
        }

        .airport-code-badge {
            display: inline-flex;
            padding: 7px 12px;
            color: var(--navy);
            background: #edf4fb;
            border: 1px solid #dce8f4;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .7px;
        }

        .airport-name {
            color: var(--navy);
            font-weight: 700;
        }

        .airport-form {
            max-width: 1000px;
        }

        .airport-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4px 22px;
        }

        .airport-form-note {
            background: #f6f9fd;
            border: 1px solid #e4eaf2;
            border-radius: 10px;
            color: #62758a;
            padding: 15px 17px;
            font-size: 11px;
            line-height: 1.8;
            margin-bottom: 23px;
        }

        .airport-form .form-group input {
            font-family: inherit;
        }

        .airport-form .form-group input::placeholder {
            color: #98a7b7;
        }

        .airport-form .form-actions {
            border-top: 1px solid var(--border);
            padding-top: 22px;
        }

        .airport-empty {
            padding: 36px 15px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        .airport-table td {
            vertical-align: middle;
        }

        @media (max-width: 760px) {

            .airport-summary,
            .airport-form-grid {
                grid-template-columns: 1fr;
            }

            .airport-summary-card {
                padding: 19px;
            }

            .airport-table {
                min-width: 650px;
            }
        }


        /* FLIGHT MANAGEMENT */

        .flight-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .flight-summary-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
        }

        .flight-summary-card .summary-label {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .flight-summary-card .summary-value {
            color: var(--navy);
            font-size: 26px;
            font-weight: 800;
        }

        .flight-summary-card .summary-note {
            margin-top: 8px;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.7;
        }

        .flight-table {
            min-width: 1100px;
        }

        .flight-table td {
            vertical-align: middle;
        }

        .flight-code-badge {
            display: inline-flex;
            padding: 8px 11px;
            background: #edf4fb;
            border: 1px solid #dce8f4;
            border-radius: 8px;
            color: var(--navy);
            font-weight: 800;
            font-size: 11px;
        }

        .flight-route {
            color: var(--navy);
            font-weight: 700;
        }

        .flight-route small {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 400;
        }

        .flight-price {
            color: var(--navy);
            font-weight: 800;
        }

        .flight-seats {
            font-weight: 700;
            color: #176b42;
        }

        .flight-seats.empty {
            color: #bf3346;
        }

        .flight-status {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .flight-status-scheduled {
            color: #176b42;
            background: #e7f7ee;
        }

        .flight-status-delayed {
            color: #a76c08;
            background: #fff4dc;
        }

        .flight-status-cancelled {
            color: #bf3346;
            background: #fdecee;
        }

        .flight-status-completed {
            color: #1267a5;
            background: #eaf3fc;
        }

        .flight-status-default {
            color: #566a80;
            background: #edf1f6;
        }

        .flight-form {
            max-width: 1100px;
        }

        .flight-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4px 22px;
        }

        .flight-form-section {
            margin: 6px 0 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
        }

        .flight-form-note {
            padding: 15px 17px;
            margin-bottom: 24px;
            color: #62758a;
            background: #f6f9fd;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 11px;
            line-height: 1.8;
        }

        .flight-form .form-actions {
            border-top: 1px solid var(--border);
            padding-top: 22px;
        }

        .flight-empty {
            padding: 35px 15px;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 1000px) {
            .flight-summary {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .flight-form-grid {
                grid-template-columns: 1fr;
            }

            .flight-summary-card {
                padding: 19px;
            }
        }

        /* INVOICE & REFUND MANAGEMENT */

        .finance-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .finance-stat {
            min-width: 0;
            padding: 23px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
            position: relative;
        }

        .finance-stat::before {
            content: "";
            position: absolute;
            top: 0;
            left: 18px;
            right: 18px;
            height: 3px;
            border-radius: 0 0 4px 4px;
            background: var(--gold);
        }

        .finance-stat-label {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 13px;
            line-height: 1.7;
        }

        .finance-stat-value {
            color: var(--navy);
            font-size: clamp(18px, 2vw, 26px);
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .finance-stat-note {
            margin-top: 8px;
            font-size: 10px;
            color: var(--muted);
        }

        .finance-panel {
            padding: 25px;
            margin-bottom: 24px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
        }

        .finance-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .finance-panel-header h2 {
            font-size: 17px;
            color: var(--navy);
        }

        .finance-panel-header p {
            font-size: 11px;
            color: var(--muted);
            margin-top: 7px;
        }

        .finance-filters {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(180px, 1fr) auto;
            gap: 15px;
            align-items: end;
        }

        .finance-field label {
            display: block;
            color: #40536a;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 9px;
        }

        .finance-field input,
        .finance-field select {
            width: 100%;
            min-height: 43px;
            padding: 11px 13px;
            border: 1px solid #dce4ee;
            border-radius: 9px;
            background: white;
            color: var(--text);
            outline: none;
            font-size: 12px;
        }

        .finance-field input:focus,
        .finance-field select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(18, 103, 165, .09);
        }

        .finance-filter-actions {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
        }

        .finance-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .finance-table {
            width: 100%;
            min-width: 930px;
            border-collapse: collapse;
        }

        .finance-table th,
        .finance-table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .finance-table th {
            background: #f4f7fb;
            color: var(--navy);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .finance-table td {
            font-size: 11px;
        }

        .finance-code {
            display: inline-flex;
            padding: 8px 11px;
            color: var(--navy);
            background: #edf4fb;
            border: 1px solid #dce8f4;
            border-radius: 8px;
            font-weight: 800;
        }

        .finance-person {
            display: block;
            color: var(--navy);
            font-weight: 700;
        }

        .finance-subtext {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 10px;
            overflow-wrap: anywhere;
        }

        .finance-amount {
            font-weight: 800;
            color: var(--navy);
            white-space: nowrap;
        }

        .finance-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
            background: #edf1f6;
            color: #566a80;
        }

        .finance-badge-pending,
        .finance-badge-pending_refund {
            background: #fff4dc;
            color: #a76c08;
        }

        .finance-badge-pending_approval {
            background: #e8f1ff;
            color: #2563eb;
        }

        .finance-badge-paid,
        .finance-badge-completed {
            background: #e7f7ee;
            color: #16834a;
        }

        .finance-badge-failed {
            background: #fdecee;
            color: #bf3346;
        }

        .finance-badge-refund_pending {
            background: #f1e8ff;
            color: #8438c0;
        }

        .finance-badge-cancelled,
        .finance-badge-no_refund {
            background: #edf1f6;
            color: #566a80;
        }

        .finance-empty {
            padding: 35px 15px !important;
            text-align: center !important;
            color: var(--muted);
        }

        .finance-pagination {
            margin-top: 22px;
        }

        .finance-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .finance-detail-card {
            min-width: 0;
            padding: 25px;
            margin-bottom: 22px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
        }

        .finance-detail-card h2 {
            margin-bottom: 18px;
            color: var(--navy);
            font-size: 17px;
            font-weight: 700;
        }

        .finance-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 18px;
            padding: 13px 0;
            border-bottom: 1px solid var(--border);
            font-size: 11px;
            line-height: 1.7;
        }

        .finance-detail-row:last-child {
            border-bottom: none;
        }

        .finance-detail-row>span:first-child {
            color: var(--muted);
            flex-shrink: 0;
        }

        .finance-detail-row strong {
            color: var(--navy);
            text-align: right;
            overflow-wrap: anywhere;
        }

        .finance-total {
            color: var(--navy);
            font-size: 23px;
            font-weight: 800;
        }

        .finance-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin: 22px 0;
        }

        .finance-notice {
            padding: 16px 18px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            color: #92400e;
            font-size: 12px;
            line-height: 1.8;
        }

        .finance-detail-table {
            min-width: 600px;
        }

        @media (max-width: 1100px) {
            .finance-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .finance-filters {
                grid-template-columns: 1fr 1fr;
            }

            .finance-filter-actions {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 760px) {

            .finance-stats,
            .finance-filters,
            .finance-detail-grid {
                grid-template-columns: 1fr;
            }

            .finance-filter-actions {
                grid-column: auto;
            }

            .finance-panel,
            .finance-detail-card {
                padding: 18px;
            }

            .finance-detail-row {
                flex-wrap: wrap;
            }
        }

        /* CUSTOMER MANAGEMENT */

        .customer-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .customer-filters {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .customer-filters .finance-filter-actions {
            grid-column: auto;
        }

        @media (max-width: 1100px) {
            .customer-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {

            .customer-stats,
            .customer-filters {
                grid-template-columns: 1fr;
            }

            .customer-filters .finance-filter-actions {
                grid-column: auto;
            }
        }

        /* AIRLINE MANAGEMENT */

        .airline-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .airline-filters {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .airline-filters .finance-filter-actions {
            grid-column: auto;
        }

        .airline-form-panel {
            max-width: 1000px;
        }

        .airline-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .airline-error {
            margin-top: 7px;
            color: #bf3346;
            font-size: 11px;
        }

        @media (max-width: 1100px) {
            .airline-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {

            .airline-stats,
            .airline-filters,
            .airline-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <div class="brand-icon">✈</div>

            <div>
                <div class="brand-name">
                    Sky<span>Booking</span>
                </div>
                <div class="brand-caption">Administration</div>
            </div>
        </a>

        <div class="nav-caption">Quản trị hệ thống</div>

        <nav class="side-nav">
            <a href="{{ route('admin.dashboard') }}"
                class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="side-icon">⌂</span>
                <span>Tổng quan Admin</span>
            </a>

            <a href="{{ route('admin.airports.index') }}"
                class="side-link {{ request()->routeIs('admin.airports.*') ? 'active' : '' }}">
                <span class="side-icon">✧</span>
                <span>Quản lý sân bay</span>
            </a>

            <a href="{{ route('admin.airlines.index') }}"
                class="side-link {{ request()->routeIs('admin.airlines.*') ? 'active' : '' }}">
                <span class="side-icon">✈</span>
                <span>Quản lý hãng hàng không</span>
            </a>

            <a href="{{ route('admin.flights.index') }}"
                class="side-link {{ request()->routeIs('admin.flights.*') ? 'active' : '' }}">
                <span class="side-icon">✈</span>
                <span>Quản lý chuyến bay</span>
            </a>

            <a href="{{ route('admin.invoices.index') }}"
                class="side-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <span class="side-icon">▤</span>
                <span>Quản lý hóa đơn</span>
            </a>

            <a href="{{ route('admin.refunds.index') }}"
                class="side-link {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
                <span class="side-icon">↺</span>
                <span>Quản lý hoàn tiền</span>
            </a>

            <a href="{{ route('admin.customers.index') }}"
                class="side-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <span class="side-icon">♙</span>
                <span>Quản lý khách hàng</span>
            </a>
        </nav>

        <div class="nav-caption">Tiện ích</div>

        <nav class="side-nav">
            <a href="{{ route('ticket.lookup.form') }}"
                class="side-link {{ request()->routeIs('ticket.lookup.*') ? 'active' : '' }}">
                <span class="side-icon">▤</span>
                <span>Tra cứu vé</span>
            </a>
        </nav>

        <div class="sidebar-spacer"></div>

        <div class="sidebar-promo">
            <h3>Trung tâm quản trị</h3>
            <p>
                Theo dõi hoạt động đặt vé, chuyến bay,
                thanh toán và hoàn tiền trên hệ thống SkyBooking.
            </p>
        </div>

        <div class="sidebar-bottom">
            SKYBOOKING ADMIN<br>
            Hệ thống quản lý đặt vé máy bay
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <div class="topbar-eyebrow">
                    QUẢN TRỊ HỆ THỐNG SKYBOOKING
                </div>

                <div class="topbar-title">
                    @yield(
                    'topbar-title',
                    View::yieldContent('title', 'Quản trị hệ thống')
                    )
                </div>
            </div>

            <div class="account-area">
                @auth
                <span class="welcome">
                    Xin chào,
                    <strong>{{ Auth::user()->name }}</strong>
                </span>

                <a href="{{ route('profile.index') }}"
                    class="top-button">
                    Tài khoản
                </a>

                <a href="{{ route('admin.dashboard') }}"
                    class="top-button">
                    Tổng quan
                </a>

                <form action="{{ route('logout') }}"
                    method="POST"
                    class="logout-form">
                    @csrf

                    <button type="submit"
                        class="top-button primary">
                        Đăng xuất
                    </button>
                </form>
                @endauth
            </div>
        </header>

        <section class="content">
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </section>
    </main>
</body>

</html>