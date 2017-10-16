//
//  Header.h
//  UPAPayPlugin
//
//  Created by zhangyi on 10/27/15.
//  Copyright © 2015 UnionPay. All rights reserved.
//

#import <Foundation/Foundation.h>


typedef NS_ENUM(NSInteger,UPPaymentResultStatus) {

    UPPaymentResultStatusSuccess,       //支付成功
    UPPaymentResultStatusFailure,       //支付失败
    UPPaymentResultStatusCancel,        //支付取消
    UPPaymentResultStatusUnknownCancel   //支付取消，交易已发起，状态不确定，商户需查询商户后台确认支付状态
};


@interface UPPayResult:NSObject
@property UPPaymentResultStatus paymentResultStatus;
@property (nonatomic,strong) NSString* errorDescription;
@property (nonatomic,strong) NSString* otherInfo;
@end



@protocol UPAPayPluginDelegate <NSObject>
-(void) UPAPayPluginResult:(UPPayResult *) payResult;
@end