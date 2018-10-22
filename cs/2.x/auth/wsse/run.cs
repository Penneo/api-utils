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
            if (args.Length < 3) {
                Console.WriteLine("Parameters required: endpoint, key, secret");
                Environment.Exit(-1);
            }

            string endpoint   = args[0];
            string key        = args[1];
            string secret     = args[2];

            PenneoConnector c = new PenneoConnector(key, secret, endpoint);
            c.Logger = new Logger();
            run(c);
        }

        public static void run(PenneoConnector c)
        {
            RestConnector connector = new RestConnector(c);
            var response = connector.InvokeRequest("user");

            Console.WriteLine(response.StatusCode);
            Console.WriteLine(response.Content);
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            Console.WriteLine(severity + ": " + message);
        }
    }
}
