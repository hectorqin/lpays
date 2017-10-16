//
//  UPViewController.m
//  UPPayDemo
//
//  Created by liwang on 12-11-12.
//  Copyright (c) 2012年 liwang. All rights reserved.
//
#include <sys/socket.h> // Per msqr
#include <sys/sysctl.h>
#include <net/if.h>
#include <net/if_dl.h>
#import "UPViewController.h"
#import "UPAPayPlugin.h"
#import <PassKit/PassKit.h>


#define kVCTitle          @"商户测试"
#define kBtnFirstTitle    @"获取订单，开始测试"
#define kWaiting          @"正在获取TN,请稍后..."
#define kNote             @"提示"         
#define kConfirm          @"确定"
#define kErrorNet         @"网络错误"
#define kResult           @"支付结果：%@"

#define KBtn_width       160
#define KBtn_height      40
#define KXOffSet         (self.view.frame.size.width - KBtn_width) / 2
#define KYOffSet         120
#define kWithNavigation  44

// TODO 商户需要换用自己的mertchantID
#define kAppleMerchantID        @"merchant.com.am.gu"



#define UPRelease(X) if (X !=nil) {[X release];X = nil;}

@interface UPViewController ()
{
    UIAlertView* _alertView;
    NSMutableData* _responseData;
    CGFloat _maxWidth;
    CGFloat _maxHeight;
    
    UITextField *_urlField;
    UITextField *_modeField;
    UITextField *_curField;
}

@property(nonatomic, copy)NSString *tnMode;

- (void)extendedLayout;

- (void)showAlertWait;
- (void)showAlertMessage:(NSString*)msg;
- (void)hideAlert;

- (void)startNetWithURL:(NSURL *)url;


@end

@implementation UPViewController
@synthesize contentTableView;
@synthesize tnMode;

- (void)dealloc
{
    self.contentTableView = nil;
    self.tnMode = nil;
    
    UPRelease(_responseData);
    UPRelease(_urlField);
    UPRelease(_modeField);
    
    [super dealloc];
}


- (void)viewDidLoad
{
    [super viewDidLoad];
    self.view.backgroundColor = [UIColor whiteColor];
    self.title = kVCTitle;
    
    [self extendedLayout];
    
//    CGFloat y = KYOffSet;
//    PKPaymentButton *aAPButton= [PKPaymentButton buttonWithType:PKPaymentButtonTypePlain style:PKPaymentButtonStyleBlack];
//    [aAPButton setFrame:CGRectMake(KXOffSet, y, KBtn_width, KBtn_height)];
//    [aAPButton addTarget:self action:@selector(normalPayAction:) forControlEvents:UIControlEventTouchUpInside];
//    [self.view addSubview:aAPButton];
    
    
    CGFloat y = KYOffSet;
    UIButton* btnStartPay = [UIButton buttonWithType:UIButtonTypeRoundedRect];
    [btnStartPay setTitle:@"启动AP-正式环境" forState:UIControlStateNormal];
    [btnStartPay addTarget:self action:@selector(normalPayAction:) forControlEvents:UIControlEventTouchUpInside];
    [btnStartPay setFrame:CGRectMake(KXOffSet, y, KBtn_width, KBtn_height)];
    [self.view addSubview:btnStartPay];
    
    self.view.backgroundColor = [[UIColor alloc] initWithRed:240.00/255.00 green:240.00/255.00 blue:240.00/255.00 alpha:1.0];
    
    y=y+KBtn_height+40;
    btnStartPay = [UIButton buttonWithType:UIButtonTypeRoundedRect];
    [btnStartPay setTitle:@"启动AP－PM环境" forState:UIControlStateNormal];
    [btnStartPay addTarget:self action:@selector(pmPayAction:) forControlEvents:UIControlEventTouchUpInside];
    [btnStartPay setFrame:CGRectMake(KXOffSet, y, KBtn_width, KBtn_height)];
    [self.view addSubview:btnStartPay];
    
    
    
//    y=y+KBtn_height+40;
//    btnStartPay = [UIButton buttonWithType:UIButtonTypeRoundedRect];
//    [btnStartPay setTitle:@"启动AP－测试环境" forState:UIControlStateNormal];
//    [btnStartPay addTarget:self action:@selector(testPayAction:) forControlEvents:UIControlEventTouchUpInside];
//    [btnStartPay setFrame:CGRectMake(KXOffSet, y, KBtn_width, KBtn_height)];
//    [self.view addSubview:btnStartPay];
}


- (void)normalPayAction:(id)sender
{
    

    //正式环境
    self.tnMode = @"00";
    
    
    // TODO 商户需要换用自己tn获取地址
    NSURL* url = [NSURL URLWithString:@"http://101.231.114.216:1725/sim/getacptn"];
    //NSURL* url = [NSURL URLWithString:@"http://101.231.114.216:1725/sim/app.jsp?user=admin"];


    NSMutableURLRequest * urlRequest=[NSMutableURLRequest requestWithURL:url];
    NSURLConnection* urlConn = [[NSURLConnection alloc] initWithRequest:urlRequest delegate:self];
    [urlConn start];
    [self showAlertWait];
    

    
//    //直接写死tn(商户可以自己写死订单）
//    self.tnMode = @"00";
//    [UPAPayPlugin startPay:@"201511181055564938258" mode:self.tnMode viewController:self delegate:self andAPMerchantID:kAppleMerchantID];
}

- (void) pmPayAction:(id)sender {
    
    //使用PM环境
    self.tnMode = @"01";
    NSURL* url = [NSURL URLWithString:@"http://101.231.204.84:8091/sim/getacptn"];
    
    NSMutableURLRequest * urlRequest=[NSMutableURLRequest requestWithURL:url];
    NSURLConnection* urlConn = [[NSURLConnection alloc] initWithRequest:urlRequest delegate:self];
    [urlConn start];
    [self showAlertWait];
    
}

//-(void) testPayAction:(id)sendr {
//    
//    //使用PM环境
//    self.tnMode = @"02";
//    NSURL* url = [NSURL URLWithString:@"http://172.18.64.34:10305/sim/app.jsp?user=admin"];
//    
//    NSMutableURLRequest * urlRequest=[NSMutableURLRequest requestWithURL:url];
//    NSURLConnection* urlConn = [[NSURLConnection alloc] initWithRequest:urlRequest delegate:self];
//    [urlConn start];
//    [self showAlertWait];
//    
//}




- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}


- (void)extendedLayout
{
    BOOL iOS7 = [UIDevice currentDevice].systemVersion.floatValue >= 7.0;
    if (iOS7) {
        self.edgesForExtendedLayout = UIRectEdgeNone;
        self.automaticallyAdjustsScrollViewInsets = NO;
    }
    
    CGFloat offset = iOS7 ? 64 : 44;
    _maxWidth = CGRectGetWidth([UIScreen mainScreen].bounds);
    _maxHeight = CGRectGetHeight([UIScreen mainScreen].bounds)-offset;
    
    self.navigationController.navigationBar.translucent = NO;
}


- (void)startNetWithURL:(NSURL *)url
{
    [_curField resignFirstResponder];
    _curField = nil;
    [self showAlertWait];
    
    NSURLRequest * urlRequest=[NSURLRequest requestWithURL:url];
    NSURLConnection* urlConn = [[[NSURLConnection alloc] initWithRequest:urlRequest delegate:self] autorelease];
    [urlConn start];
}



#pragma mark - Alert

- (void)showAlertWait
{
    [self hideAlert];
    _alertView = [[UIAlertView alloc] initWithTitle:kWaiting message:nil delegate:self cancelButtonTitle:nil otherButtonTitles: nil];
    [_alertView show];
    UIActivityIndicatorView* aiv = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhite];
    aiv.center = CGPointMake(_alertView.frame.size.width / 2.0f - 15, _alertView.frame.size.height / 2.0f + 10 );
    [aiv startAnimating];
    [_alertView addSubview:aiv];
    [aiv release];
    [_alertView release];
}

- (void)showAlertMessage:(NSString*)msg
{
    [self hideAlert];
    _alertView = [[UIAlertView alloc] initWithTitle:kNote message:msg delegate:self cancelButtonTitle:kConfirm otherButtonTitles:nil, nil];
    [_alertView show];
    [_alertView release];
}
- (void)hideAlert
{
    if (_alertView != nil)
    {
        [_alertView dismissWithClickedButtonIndex:0 animated:NO];
        _alertView = nil;
    }
}

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    _alertView = nil;
}

#pragma mark - connection

- (void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse*)response
{
    NSHTTPURLResponse* rsp = (NSHTTPURLResponse*)response;
    NSInteger code = [rsp statusCode];
    if (code != 200)
    {
        
        [self showAlertMessage:kErrorNet];
        [connection cancel];
    }
    else
    {
        UPRelease(_responseData);
        _responseData = [[NSMutableData alloc] init];
    }
}

- (void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data
{
    [_responseData appendData:data];
}

- (void)connectionDidFinishLoading:(NSURLConnection *)connection
{
    [self hideAlert];
    NSString* tn = [[NSMutableString alloc] initWithData:_responseData encoding:NSUTF8StringEncoding];
    if (tn != nil && tn.length > 0)
    {
        if([PKPaymentAuthorizationViewController canMakePaymentsUsingNetworks:@[PKPaymentNetworkChinaUnionPay]])
        {

            [UPAPayPlugin startPay:tn mode:self.tnMode viewController:self delegate:self andAPMechantID:kAppleMerchantID];
        }

    }
    [tn release];
}

-(void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error
{
    [self showAlertMessage:kErrorNet];
}


#pragma mark -
#pragma mark 响应控件返回的支付结果
#pragma mark -
- (void)UPAPayPluginResult:(UPPayResult *)result
{
    if(result.paymentResultStatus == UPPaymentResultStatusSuccess) {
        NSString *otherInfo = result.otherInfo?result.otherInfo:@"";
        NSString *successInfo = [NSString stringWithFormat:@"支付成功\n%@",otherInfo];
        [self showAlertMessage:successInfo];
    }
    else if(result.paymentResultStatus == UPPaymentResultStatusCancel){

        [self showAlertMessage:@"支付取消"];
    }
    else if (result.paymentResultStatus == UPPaymentResultStatusFailure) {
        
        NSString *errorInfo = [NSString stringWithFormat:@"%@",result.errorDescription];
        [self showAlertMessage:errorInfo];
    }
    else if (result.paymentResultStatus == UPPaymentResultStatusUnknownCancel)  {
        
        //TODO UPPAymentResultStatusUnknowCancel表示发起支付以后用户取消，导致支付状态不确认，需要查询商户后台确认真实的支付结果
        NSString *errorInfo = [NSString stringWithFormat:@"支付过程中用户取消了，请查询后台确认订单"];
        [self showAlertMessage:errorInfo];
        
    }
}



#pragma mark UITextFieldDelegate
- (void)textFieldDidBeginEditing:(UITextField *)textField
{
    _curField = textField;
}

@end
