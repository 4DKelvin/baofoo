#!/usr/local/bin/python
# -*- coding: utf-8 -*-
#
import tornado.ioloop
import subprocess
import json
import tornado.web


class Handler(tornado.web.RequestHandler):
    def get(self):
        data = dict()
        try:
            data["MemberID"] = str(self.get_argument('MemberID', ''))
            data["TerminalID"] = str(self.get_argument('TerminalID', ''))
            data["TransID"] = str(self.get_argument('TransID', ''))
            data["Result"] = str(self.get_argument('Result', ''))
            data["ResultDesc"] = str(self.get_argument('ResultDesc', ''))
            data["FactMoney"] = str(self.get_argument('FactMoney', ''))
            data["AdditionalInfo"] = str(self.get_argument('AdditionalInfo', ''))
            data["SuccTime"] = str(self.get_argument('SuccTime', ''))
            data["Md5Sign"] = str(self.get_argument('Md5Sign', ''))
            data["BankID"] = str(self.get_argument('BankID', ''))
        except:
            return self.finish({'data': '参数错误', 'code': 500})
        proc = subprocess.Popen("php decrypt.php "
                                + data["MemberID"] + ' '
                                + data["TerminalID"] + ' '
                                + data["TransID"] + ' '
                                + data["Result"] + ' '
                                + data["ResultDesc"] + ' '
                                + data["FactMoney"] + ' '
                                + data["AdditionalInfo"] + ' '
                                + data["SuccTime"] + ' '
                                + data["Md5Sign"] + ' '
                                + data["BankID"], shell=True,
                                stdout=subprocess.PIPE)
        script_response = proc.stdout.read()
        return self.finish(json.loads(script_response))

    def post(self):
        try:
            PayID = str(self.get_argument('PayID'))
            Money = str(self.get_argument('Money'))
        except:
            return self.finish({'data': '参数错误', 'code': 500})

        proc = subprocess.Popen("php encrypt.php " + PayID + " " + Money, shell=True,
                                stdout=subprocess.PIPE)
        script_response = proc.stdout.read()
        return self.finish(json.loads(script_response))


settings = {
    'template_path': 'views',
}

application = tornado.web.Application([
    (r"/baofoo", Handler),
], **settings)

if __name__ == "__main__":
    application.listen(8090)
    tornado.ioloop.IOLoop.instance().start()
