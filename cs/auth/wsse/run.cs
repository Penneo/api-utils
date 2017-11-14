using System;
using Penneo;
using System.Collections.Generic;
// using System.Linq;
using System.Diagnostics;

namespace Penneo
{
    public class AuthenticatedUser
    {
        public static void Main(string[] args)
        {
            if (args.Length != 3) {
                Console.WriteLine("Parameters required: endpoint, key, secret");
                Environment.Exit(-1);
            }

            string endpoint   = args[0];
            string key        = args[1];
            string secret     = args[2];

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            run();
        }

        public static void run()
        {
            RestConnector connector = new RestConnector();
            var response = connector.InvokeRequest("user");

            Console.WriteLine(response.StatusCode);
            Console.WriteLine(response.Content);
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            // Debug.WriteLine(severity + ": " + message);
        }
    }
}
