# PayPal Integration Setup Instructions

## ✅ Implementation Complete

The PayPal payment integration has been successfully implemented with the following components:

### Files Created/Modified:

#### New Files:

-   ✅ `config/paypal.php` - PayPal configuration
-   ✅ `app/Services/Payment/PayPalService.php` - PayPal API service
-   ✅ `app/Http/Controllers/PayPalController.php` - Handles success/cancel/failure callbacks
-   ✅ `app/Http/Controllers/PayPalWebhookController.php` - Handles webhook verification
-   ✅ `database/migrations/2025_12_29_235052_add_payment_gateway_id_to_orders_table.php` - Database migration

#### Modified Files:

-   ✅ `app/Http/Controllers/CheckoutController.php` - Added PayPal payment processing
-   ✅ `routes/web.php` - Added PayPal routes and webhook endpoint
-   ✅ `app/Http/Middleware/VerifyCsrfToken.php` - Excluded webhook from CSRF
-   ✅ `resources/views/checkout/index.blade.php` - Added PayPal payment option
-   ✅ `app/Models/Order.php` - Added payment_gateway_id field

---

## 🔧 Setup Steps

### 1. Add Environment Variables

Add these variables to your `.env` file:

```env
# PayPal Configuration
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=AaYJYDiJ0ZtLpf6Nbt8Mbvraz0BvS-KAzNWzTmKgULn5Iq0kyd-L45fKpeV58tDm7fKzeYQZ7mvtRhyV
PAYPAL_SECRET=EAtUk228ih1b5WrJTXO3lnH34wZN-H7kUXOMfYR3A1CTEixucEq1-fLpCaUdiGXSJqMR04jnuGcOUx4D
PAYPAL_CURRENCY=SAR

# PayPal Redirect URLs (Update with your actual domain)
PAYPAL_SUCCESS_URL=${APP_URL}/paypal/success
PAYPAL_CANCEL_URL=${APP_URL}/paypal/cancel
PAYPAL_FAILURE_URL=${APP_URL}/paypal/failure

# PayPal Webhook (Will be generated in step 3)
PAYPAL_WEBHOOK_ID=
```

**Important:** Replace `${APP_URL}` with your actual production URL (e.g., `https://yourdomain.com`)

### 2. Run Database Migration

When your MySQL server is running, execute:

```bash
php artisan migrate
```

This will add the `payment_gateway_id` column to the `orders` table.

### 3. Configure PayPal Webhook

#### Step 3.1: Login to PayPal Developer Dashboard

1. Go to: https://developer.paypal.com/dashboard/
2. Login with your PayPal business account
3. Navigate to **Apps & Credentials** → Select **Live** mode

#### Step 3.2: Create Webhook

1. Scroll down to **Webhooks** section
2. Click **Add Webhook**
3. Set **Webhook URL** to: `https://yourdomain.com/webhook/paypal` (replace with your actual domain)
4. Select the following **Event types**:
    - ✅ `Checkout order approved` (CHECKOUT.ORDER.APPROVED)
    - ✅ `Payment capture completed` (PAYMENT.CAPTURE.COMPLETED)
    - ✅ `Payment capture denied` (PAYMENT.CAPTURE.DENIED)
    - ✅ `Payment capture refunded` (PAYMENT.CAPTURE.REFUNDED)
5. Click **Save**

#### Step 3.3: Copy Webhook ID

1. After creating the webhook, click on it to view details
2. Copy the **Webhook ID** (it looks like: `1AB2345C6D789E0F1`)
3. Add it to your `.env` file:
    ```env
    PAYPAL_WEBHOOK_ID=1AB2345C6D789E0F1
    ```

### 4. Clear Configuration Cache

After adding environment variables:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🎯 Payment Flow

### User Journey:

1. User adds courses to cart
2. User proceeds to checkout
3. User selects **PayPal** as payment method
4. User clicks "Complete Order"
5. User is redirected to PayPal to complete payment
6. After payment:
    - **Success**: User is redirected back to success page, enrollments are activated
    - **Cancel**: User is redirected to checkout with items restored to cart
    - **Failure**: User is redirected to checkout with error message

### Webhook Flow (Background Verification):

1. PayPal sends webhook notification when payment is captured
2. System verifies webhook signature for security
3. System updates order status to 'completed'
4. System activates pending enrollments
5. System sends enrollment notification emails

---

## 🧪 Testing Checklist

### Before Going Live:

-   [ ] Verify `.env` has correct PayPal credentials
-   [ ] Confirm `PAYPAL_MODE=live` (not sandbox)
-   [ ] Test complete payment flow end-to-end
-   [ ] Verify webhook is receiving events (check Laravel logs)
-   [ ] Test payment cancellation
-   [ ] Test with coupon codes applied
-   [ ] Verify enrollments are activated after payment
-   [ ] Verify email notifications are sent
-   [ ] Test order history displays correct information

### Testing Commands:

```bash
# Monitor webhook activity
php artisan queue:work --verbose

# Check logs
tail -f storage/logs/laravel.log
```

---

## 🔒 Security Features

✅ **Webhook Signature Verification**: All webhooks are verified using PayPal's verification API  
✅ **CSRF Protection**: Only webhook endpoint is excluded from CSRF  
✅ **User Authorization**: All callbacks verify the order belongs to the authenticated user  
✅ **Amount Validation**: Order amounts are validated against PayPal responses  
✅ **Transaction Logging**: All payment attempts and webhooks are logged

---

## 📊 Available Routes

### User-Facing Routes (Authenticated):

-   `GET /paypal/success` - Payment success callback
-   `GET /paypal/cancel` - Payment cancellation callback
-   `GET /paypal/failure` - Payment failure callback

### Webhook Route (Public):

-   `POST /webhook/paypal` - PayPal webhook handler (CSRF excluded)

---

## 💡 Supported Payment Methods

After this integration, your system now supports:

1. **Free Enrollment** - For free courses
2. **Credit Card (Visa)** - Via payment gateway (placeholder)
3. **Tabby** - Buy now, pay later
4. **PayPal** - ✨ NEW - PayPal payments in SAR

---

## 🐛 Troubleshooting

### Issue: "Unable to initiate PayPal payment"

**Solution:** Check Laravel logs (`storage/logs/laravel.log`). Usually caused by:

-   Incorrect API credentials
-   Network issues
-   Invalid currency configuration

### Issue: "Payment information not found"

**Solution:** Session was cleared. User needs to retry checkout.

### Issue: Webhook not receiving events

**Solution:**

1. Verify webhook URL is publicly accessible (not localhost)
2. Check webhook configuration in PayPal Dashboard
3. Ensure `PAYPAL_WEBHOOK_ID` is set in `.env`
4. Check Laravel logs for verification errors

### Issue: Order status not updating after payment

**Solution:**

1. Check webhook logs in `storage/logs/laravel.log`
2. Verify webhook signature verification is passing
3. Ensure `payment_gateway_id` column exists in orders table

---

## 📞 Support

For PayPal API issues:

-   PayPal Developer Community: https://www.paypal-community.com/
-   PayPal Developer Docs: https://developer.paypal.com/docs/

For implementation issues:

-   Check Laravel logs: `storage/logs/laravel.log`
-   Enable debug mode temporarily: `APP_DEBUG=true` in `.env`

---

## 🎉 You're All Set!

Your Laravel courses application now has full PayPal integration with:

-   ✅ Secure payment processing
-   ✅ Webhook verification
-   ✅ Automatic enrollment activation
-   ✅ Email notifications
-   ✅ Order tracking
-   ✅ SAR currency support

**Next Step:** Run the migration and configure the webhook in PayPal Dashboard!
