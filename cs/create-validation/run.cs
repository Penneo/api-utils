using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Pennneo
{
    public class CreateValidation
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
            var v = new Validation("John Doe", "john@doe.com");
            v.Title = "Sample Validation";

            // Validation email
            v.EmailSubject = "Validation inquiry";
            v.EmailText    = "Dear john. Please validate yourself using this link:";

            v.Persist();
            v.Send();
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            Debug.WriteLine(severity + ": " + message);
        }
    }
}
