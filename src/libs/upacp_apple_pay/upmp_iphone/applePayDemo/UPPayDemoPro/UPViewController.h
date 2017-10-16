//
//  UPViewController.h
//  UPPayDemo
//
//  Created by liwang on 12-11-12.
//  Copyright (c) 2012年 liwang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "UPAPayPluginDelegate.h"


@interface UPViewController : UIViewController<UPAPayPluginDelegate, UIAlertViewDelegate>


@property(nonatomic, retain)UITableView *contentTableView;


@end
