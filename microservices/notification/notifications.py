import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email_validator import validate_email, EmailNotValidError
from config import EMAIL_CONFIG

def send_email(recipient, subject, message):
    """
    Send email using SMTP with proper HTML formatting
    """
    try:
        print(f"📧 Attempting to send email to: {recipient}")
        print(f"📧 Using SMTP: {EMAIL_CONFIG['smtp_server']}:{EMAIL_CONFIG['smtp_port']}")
        
        # Validate email address
        validate_email(recipient)
        
        # Create message
        msg = MIMEMultipart('alternative')  # ✅ Allow both plain and HTML
        
        # ✅ Add proper sender name format
        msg['From'] = f"{EMAIL_CONFIG.get('from_name', 'Hospital System')} <{EMAIL_CONFIG['username']}>"
        msg['To'] = recipient
        msg['Subject'] = subject
        
        # ✅ Create both plain text and HTML versions
        plain_text = f"""
Hospital Notification

{message}

---
This is an automated message from the Hospital Management System.
Please do not reply to this email.
        """.strip()
        
        # Create HTML body (your existing HTML is great!)
        html_body = f"""
        <html>
        <body>
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h2 style="color: #007bff; margin-bottom: 20px;">🏥 Hospital Notification</h2>
                    <div style="background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <p style="font-size: 16px; line-height: 1.5; color: #333;">{message}</p>
                    </div>
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                        <p style="font-size: 12px; color: #6c757d; margin: 0;">
                            This is an automated message from the Hospital Management System.<br>
                            Please do not reply to this email.
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        """
        
        # ✅ Attach both versions
        msg.attach(MIMEText(plain_text, 'plain'))
        msg.attach(MIMEText(html_body, 'html'))
        
        # ✅ Enhanced SMTP connection with better error handling
        try:
            print(f"🔐 Connecting to Gmail SMTP...")
            server = smtplib.SMTP(EMAIL_CONFIG['smtp_server'], EMAIL_CONFIG['smtp_port'])
            server.set_debuglevel(0)  # Set to 1 for verbose SMTP debugging
            
            print(f"🔒 Starting TLS encryption...")
            server.starttls()  # Enable encryption
            
            print(f"👤 Authenticating with Gmail...")
            server.login(EMAIL_CONFIG['username'], EMAIL_CONFIG['password'])
            
            print(f"📤 Sending message...")
            server.send_message(msg)
            server.quit()
            
            print(f"✅ Email sent successfully to {recipient}")
            print(f"📧 Subject: {subject}")
            print(f"💬 Message preview: {message[:100]}...")
            return True
            
        except smtplib.SMTPAuthenticationError as e:
            print(f"❌ Gmail Authentication Error: {e}")
            print("💡 Check your App Password and 2-Factor Authentication")
            print(f"📧 [EMAIL FALLBACK] To: {recipient}, Subject: {subject}")
            return False
            
        except smtplib.SMTPException as e:
            print(f"❌ SMTP Error: {e}")
            print(f"📧 [EMAIL FALLBACK] To: {recipient}, Subject: {subject}")
            return False
            
    except EmailNotValidError as e:
        print(f"❌ Invalid email address {recipient}: {e}")
        return False
    except Exception as e:
        print(f"❌ Unexpected error sending email: {e}")
        print(f"📧 [EMAIL FALLBACK] To: {recipient}, Subject: {subject}, Message: {message}")
        return False

def send_sms(phone_number, message):
    """
    Placeholder for SMS - to be implemented later
    """
    print(f"📱 [SMS PLACEHOLDER] To: {phone_number}, Message: {message}")
    # TODO: Implement SMS service (Twilio, etc.)

def send_push_notification(device_id, message):
    """
    Placeholder for Push Notifications - to be implemented later
    """
    print(f"🔔 [PUSH PLACEHOLDER] To: {device_id}, Message: {message}")
    # TODO: Implement push notification service (FCM, APNs)