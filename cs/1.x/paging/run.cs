using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Penneo
{
    public class CustomerUsers
    {
        public static void Main(string[] args)
        {
            if (args.Length != 4) {
                Console.WriteLine("Parameters required: endpoint, key, secret, folderId");
                Environment.Exit(-1);
            }

            string endpoint   = args[0];
            string key        = args[1];
            string secret     = args[2];
            string folderId   = args[3];

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            var page = getPage(folderId, 1, 10);
            Console.WriteLine(page);
        }

        public static String getPage(String folderId, int page, int perPage)
        {
            RestConnector connector = new RestConnector();
            var response = connector.InvokeRequest("/folders/" + folderId + "/casefiles"
                                                   + "?page=" + page
                                                   + "&per_page=" + perPage
                                                   );
            return response.Content;
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
