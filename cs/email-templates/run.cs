using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Penneo
{
    public class EmailTemplates
    {
        public static void Main(string[] args)
        {
            if (args.Length != 3) {
                Console.WriteLine("Parameters required: endpoint, key, secret");
                Environment.Exit(-1);
            }

            string endpoint = args[0];
            string key      = args[1];
            string secret   = args[2];

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            run();
        }

        public static void run()
        {
            var templates = Query.FindAll<MessageTemplate>();
            foreach (MessageTemplate template in templates)
            {
                Console.WriteLine("- " + template.Id + " : " + template.Title + " : " + template.Subject);
            }
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            // Console.WriteLine(severity + ": " + message);
        }
    }
}
