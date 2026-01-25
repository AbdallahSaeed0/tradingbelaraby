<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        @page {
            margin: 0;
            size: landscape;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .certificate-container {
            width: 11in;
            height: 8.5in;
            margin: 0 auto;
            background: white;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        .certificate-border {
            border: 20px solid #d4af37;
            height: calc(100% - 40px);
            width: calc(100% - 40px);
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .certificate-content {
            padding: 80px 60px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .certificate-subtitle {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 60px;
        }
        .certificate-name {
            font-size: 42px;
            font-weight: bold;
            color: #2c3e50;
            margin: 40px 0;
            padding: 20px;
            border-bottom: 3px solid #d4af37;
            display: inline-block;
        }
        .certificate-text {
            font-size: 20px;
            color: #34495e;
            line-height: 1.8;
            margin: 30px 0;
        }
        .course-name {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
        }
        .certificate-date {
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 60px;
        }
        .certificate-seal {
            margin-top: 40px;
        }
        .seal {
            width: 120px;
            height: 120px;
            border: 5px solid #d4af37;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #d4af37;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-content">
                <div class="certificate-title">Certificate of Completion</div>
                <div class="certificate-subtitle">This is to certify that</div>
                
                <div class="certificate-name">{{ $studentName }}</div>
                
                <div class="certificate-text">
                    has successfully completed the course
                </div>
                
                <div class="course-name">{{ $courseName }}</div>
                
                <div class="certificate-date">
                    Completed on {{ $completionDate }}
                </div>
                
                <div class="certificate-seal">
                    <div class="seal">
                        SEAL
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
